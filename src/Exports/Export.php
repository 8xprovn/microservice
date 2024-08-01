<?php

namespace Microservices\Exports;

use Illuminate\Support\Facades\App;
use Microservices\models\System\Exports;

class Export
{
    protected $_service_code;
    protected $_listener_file;

    public function __construct()
    {
        $this->_listener_file = '\App\Listeners\ExportLogsSubscriber\store()';
        $this->_service_code = 'erp_system_backend_v2';
    }

    public function __call($method, $arg = [])
    {
        $_model = App::make(Exports::class);
        // validate data
        $input = array_merge(['job' => $arg[0]], $arg[1] ?? []);
        $_service = $input['service'] = config('app.service_code');

        // kiểm tra người dùng đã export bao nhiêu lần gần nhất
        $rows = $_model->all([
            'created_by' => $input['created_by'],
            'job' => $input['job'],
            'service' => $_service,
            'created_time' => ['gte' => date('Y-m-d') . ' 00:00:00', 'lte' => date('Y-m-d') . ' 23:59:00']
        ], ['order_by' => array('created_time', 'desc')]);

        $_first = $rows[0] ?? [];

        if (!empty($_first)) {
            // kiểm tra xem job đó đã xong chưa và thời gian export cách nhau khoản bn phút // && (time() - $_first['created_time']) < 10 * 60
            if ($_first['status'] == 'open') return array('status' => 'error', 'message' => "Bạn đang có 1 job export chưa thực hiện xong, bạn vui lòng chờ !!!");
        }
        $input['uuid'] =  $arg[1]['uuid'] = $this->generate_uuid();

        \App\Jobs\BusJob::dispatch($this->_listener_file, $input)->onQueue($this->_service_code);

        $func = '\Microservices\Jobs\\' . $method;
        dispatch(new $func(...$arg))->onQueue($_service);
        //// call event local////
        if (class_exists('\App\Jobs\\' . $arg[0])) {
            $eventFunction = '\App\Jobs\\' . $arg[0];
            $dataEvent = $arg;
            unset($dataEvent[0]);
            $r = dispatch(new $eventFunction(...$dataEvent))->onQueue($_service);
        }
        return array('status' => 'success', 'message' => "Export thành công, bạn vui lòng chờ !!!");
    }

    private function generate_uuid()
    {
        $data = bin2hex(random_bytes(16));
        $data[12] = '4'; // Phiên bản 4
        $data[16] = dechex((hexdec($data[16]) & 0x3) | 0x8); // Biến thể DCE 1.1

        $uuid = substr($data, 0, 8) . '-' .
            substr($data, 8, 4) . '-' .
            substr($data, 12, 4) . '-' .
            substr($data, 16, 4) . '-' .
            substr($data, 20, 12);
        return $uuid . '-' . time();
    }
}
