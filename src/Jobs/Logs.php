<?php

namespace Microservices\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Microservices\models\System\Logs as ModelLogs;

class Logs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = $this->data;

        $log_model = App::make(ModelLogs::class);
        $data = collect($data)->only(['relate_type', 'relate_id', 'created_by', 'data_olds', 'data_news', 'action', 'service', 'time_update'])->toArray();
        $data_old = $data['data_olds'] ?? [];
        $data_new = $data['data_news'] ?? [];
        $action = strtolower($data['action'] ?? '');

        if (empty($action)) return;
        if (!in_array($action, ['delete']) && empty($data_new)) return;

        $changes = $this->findChanges($data_old, $data_new);
        $separatedChanges = $this->separateChanges($changes);
        $arr = Arr::only($data, ['relate_type', 'relate_id', 'created_by',  'action', 'service', 'time_update']);

        if ($action == 'delete')  return $log_model->create($arr);

        if (!empty($separatedChanges['news'])) {
            $param = array_merge($arr, $separatedChanges);
            return $log_model->create($param);
        }

        return;
    }

    private  function separateChanges($changes)
    {
        $oldValues = [];
        $newValues = [];

        foreach ($changes as $key => $change) {
            if (is_array($change) && isset($change['olds']) && isset($change['news'])) {
                $oldValues[$key] = $change['olds'];
                $newValues[$key] = $change['news'];
            } elseif (is_array($change)) {
                // Xử lý đệ quy cho các mảng lồng nhau
                $nested = $this->separateChanges($change);
                if (!empty($nested['olds'])) {
                    $oldValues[$key] = $nested['olds'];
                }
                if (!empty($nested['news'])) {
                    $newValues[$key] = $nested['news'];
                }
            }
        }

        return ['olds' => $oldValues, 'news' => $newValues];
    }

    private function findChanges($old, $new)
    {
        $changes = [];
        foreach ($new as $key => $value) {
            if (array_key_exists($key, $old)) {
                if (is_array($value) && is_array($old[$key])) {
                    // Đối với mảng lồng, gọi đệ quy
                    $subChanges = $this->findChanges($old[$key], $value);
                    if (!empty($subChanges)) {
                        $changes[$key] = $subChanges;
                    }
                } else if ($value !== $old[$key]) {
                    // Chỉ lưu những giá trị thực sự thay đổi
                    $changes[$key] = ['olds' => $old[$key], 'news' => $value];
                }
            } else {
                // Nếu khóa không tồn tại trong mảng cũ, nó là một phần tử mới
                $changes[$key] = ['olds' => '', 'news' => $value];
            }
        }

        // Kiểm tra các khóa có trong mảng cũ nhưng không có trong mảng mới
        foreach ($old as $key => $value) {
            if (!array_key_exists($key, $new)) {
                $changes[$key] = ['olds' => $value, 'news' => ''];
            }
        }

        return $changes;
    }
}
