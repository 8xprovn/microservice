<?php

namespace Microservices\models\Storage;

use Illuminate\Support\Facades\Http;

class File
{
    protected $domains;
    protected $url;
    protected $hash;
    protected $_listener;
    protected $_service_code;
    public function __construct()
    {
        $this->url = env('SERVICE_UPLOAD_URL_V2', '');
        $this->hash = env('SERVICE_STORAGE_HASH_SECRET', '123456');
        $this->domains = array_values(array_filter([
            env('SERVICE_UPLOAD_URL_V2', '') . '/storage/',
            env('SERVICE_UPLOAD_URL_V2', ''),
            env('SERVICE_MEDIA_URL_R2', ''),
            env('SERVICE_MEDIA_URL', ''),
        ]));

        $this->_listener = 'App\Jobs\MoveFileUpload';
        $this->_service_code = 'erp_system_backend_v2';
    }
    public function view($params)
    {
        $url = "{$this->url}/show-files";
        $response = Http::get($url, $params);
        $result =  ['status' => $response->status()];

        if ($response->successful()) {
            $result = array_merge($response->json(), $result);
        }
        return $result;
    }
    public function token($string, $config_key_md5 = '')
    {
        if (empty($config_key_md5)) $config_key_md5 = $this->hash;
        if (is_array($string)) {
            $string = implode('-', $string);
        }
        return md5("{$config_key_md5}_{$string}");
    }

    public function show($path, $params = [])
    {
        if (empty($path)) return '';
        $path = $this->splitEncodedPath($path);

        $arrs = explode('/', trim($path, '/'));
        if (empty($arrs)) return '/';
        $channel = env('UPLOAD_CHANNEL', trim($arrs[0] ?? ''));

        $configChannels = (array) json_decode(env("STORAGE_CHANNEL", '{}'));

        $configDriver = env("STORAGE_DISK", '');

        foreach ($configChannels as $key => $v) {
            if ((is_array($v) && in_array($channel, $v)) || (is_string($v) && $channel == $v)) {
                $configDriver = $key;
                break;
            }
        }
        switch ($configDriver) {
            case "onedrive":
                $input = array_merge($params, ['path' => $path]);
                return "{$this->url}/api/files/show?" . http_build_query($input);
            case "r2":
                return env('SERVICE_MEDIA_URL_R2', '') . '/' . trim($path, '/');
            default:
                return env('SERVICE_MEDIA_URL', '') . '/' . trim($path, '/');
        }
    }

    public function move($datas = [], $dataOlds = [])
    {
        $fileNews = $fileOlds = [];
        foreach ($datas as $file) {
            if (is_array($file)) {
                $file = array_map(function ($item) {
                    $path = array_reverse(explode('path=', $item))[0] ?? '';
                    return trim($path, " \t\n\r\0\x0B'\"");
                }, $file);
                $fileNews = array_merge($fileNews, array_values($file));
            } else {
                $file = array_reverse(explode('path=', $file))[0] ?? '';
                $fileNews[] = trim($file, " \t\n\r\0\x0B'\"");
            }
        }
        if (!empty($dataOlds)) {
            foreach ($dataOlds as $file) {
                if (is_array($file)) {
                    $fileOlds = array_merge($fileOlds, array_values($file));
                } else {
                    $fileOlds[] = $file;
                }
            }
        }
        $dataFileNew = array_values(array_diff($fileNews, $fileOlds));
        $dataFileOld =  array_values(array_diff($fileOlds, $fileNews));
        if (empty($dataFileNew) && empty($dataFileOld)) return;
        return \Microservices\Jobs\BusJob::dispatch($this->_listener, ['files' => $dataFileNew, 'delete' => $dataFileOld])->onQueue($this->_service_code);
    }

    private function splitEncodedPath(string $encoded)
    {
        // 1) Giải mã URL-encoded (an toàn hơn urldecode cho path)
        return rawurldecode($encoded);
    }

    /**
     * URL tuyệt đối có thuộc một trong các base URL ENV hay không
     */
    private function isAllowedEnvUrl(string $url): bool
    {
        $bases = $this->domains;
        if (empty($bases)) return false;
        // Chuẩn hoá URL đầu vào để so sánh
        $u = $url;
        if (strpos($u, '//') === 0) {
            $u = 'http:' . $u; // thêm scheme giả để parse ok
        }
        // So khớp bằng "bắt đầu với" sau khi chuẩn hoá trailing slash
        foreach ($bases as $base) {
            if ($base === '') continue;
            $b = rtrim($base, '/');
            if (stripos($u, $b . '/') === 0 || strcasecmp(rtrim($u, '/'), $b) === 0) {
                return true;
            }
        }
        return false;
    }
    /**
     * Thay /tmp -> /src trên các field chỉ định.
     * - $fields: mảng đường dẫn dot-notation, ví dụ: ['file', 'data.file', 'datas.file', 'content']
     * - $alsoUpdateHtmlImg: true => nếu field là string HTML, sẽ sửa cả <img src|srcset> bên trong.
     */
    public function convertPathSave($params, $fields, bool $handelDomain = false): array
    {
        if (!is_array($params) || empty($fields)) return (array) $params;

        foreach ($fields as $path) {
            $this->applyTransformByPath($params, $path, function ($val) use ($handelDomain) {
                return $this->replaceTmpToSrcRecursive($val, $handelDomain);
            });
        }
        return $params;
    }

    /* =================== Helpers =================== */

    /**
     * Áp dụng 1 transform cho node theo đường dẫn dot-notation.
     * Tự lách qua mảng list nếu không có key trùng ở level hiện tại.
     */
    private function applyTransformByPath(array &$node, string $path, callable $transform): void
    {
        $parts = array_values(array_filter(explode('.', $path), 'strlen'));
        $this->walkAndTransform($node, $parts, $transform);
    }

    private function walkAndTransform(&$node, array $parts, callable $transform): void
    {
        if (empty($parts)) {
            $node = $transform($node);
            return;
        }

        if (!is_array($node)) return;

        $key = array_shift($parts);

        if (array_key_exists($key, $node)) {
            $this->walkAndTransform($node[$key], $parts, $transform);
            return;
        }

        // Nếu là list (mảng số), thử áp tiếp cho từng phần tử
        foreach ($node as &$child) {
            if (is_array($child)) {
                $this->walkAndTransform($child, array_merge([$key], $parts), $transform);
            }
        }
    }

    /**
     * Đệ quy thay /tmp -> /src cho:
     *  - String thường (đường dẫn)
     *  - HTML chứa <img> (cả src & srcset) nếu $handleHtmlImages = true
     * Đồng thời dọn '//' dư nhưng giữ nguyên 'http://', 'https://'.
     */
    private function replaceTmpToSrcRecursive($data, bool $handelDomain = false)
    {
        // String
        if (is_string($data)) {
            $str = trim($data);

            if (stripos($str, '<img') !== false) {
                // sửa trong HTML (src, srcset)
                $str = $this->replaceTmpInHtmlImages($str, $handelDomain);
                return $str;
            }
            // ❗ Không phải đường dẫn file → return luôn, không động vào
            if (!$this->isFilePath($str)) {
                return $str;
            }
            $path = $this->normalizePathString($str);
            return !empty($handelDomain) ? $this->replaceDomainInHtml($path) :  $path;
        }

        // Array
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->replaceTmpToSrcRecursive($v, $handelDomain);
            }
        }

        return $data;
    }

    private function isFilePath(string $str): bool
    {
        return preg_match('/\.(jpg|jpeg|png|gif|webp|svg|pdf|docx?|xlsx?|pptx?|zip|mp4|mp3)$/i', $str);
    }

    /**
     * Chuẩn hoá 1 đường dẫn/string:
     * - đổi /tmp/ -> /src/ (kể cả trường hợp bắt đầu bằng 'tmp/')
     * - dọn '//' dư nhưng không phá 'http(s)://'
     */
    private function normalizePathString(string $str): string
    {
        if ($str === '') return $str;

        // Chỉ xử lý khi có tmp như  .../tmp/... hoặc bắt đầu tmp/
        if (preg_match('#(^|/)tmp/#i', $str)) {
            $str = preg_replace(['#(^|/)tmp/#i', '#/tmp/#i'], '/src/', $str);
            // dọn // dư, giữ http(s)://
            $str = preg_replace('#(?<!:)//+#', '/', $str);
        }

        return trim(str_replace($this->domains, '', $str), '/');
    }

    /**
     * Thay /tmp -> /src bên trong HTML: xử lý cả src & srcset của <img>.
     */
    private function replaceTmpInHtmlImages(string $html, $isDomain = true): string
    {

        // src="..."/src='...'/src=unquoted...
        $html = preg_replace_callback(
            '/\bsrc\s*=\s*(["\']?)([^"\'>\s]+)\1/iu',
            function ($m) use ($isDomain) {
                $url  = $m[2];
                $url = array_reverse(explode('path=', $url))[0] ?? '';
                if ((preg_match('#^https?://#i',  $url) || preg_match('#^http?://#i',  $url)) && !$this->isAllowedEnvUrl($url)) {
                    return str_replace($m[2], $url, $m[0]);
                }
                $new  = $this->normalizePathString($url);
                if (!empty($isDomain)) $new = $this->replaceDomainInHtml($new);
                return str_replace($m[2], $new, $m[0]);
            },
            $html
        );
        return $html;
    }

    private function replaceDomainInHtml(string $url): string
    {
        if (empty($url)) return '';
        $url = trim(html_entity_decode($url));
        // URL tuyệt đối ngoài hệ thống thì giữ nguyên
        if ((preg_match('#^https?://#i', $url) || preg_match('#^http?://#i', $url)) && !$this->isAllowedEnvUrl($url)) {
            return $url;
        }
        return $this->show($url);
    }



    /**
     * So sánh nội dung HTML theo các field, trích <img src="..."> từ new/old,
     * rồi move các file mới xuất hiện (dispatch 1 lần).
     *
     * @param array $newParams
     * @param array $oldParams
     * @param array $arrField  Danh sách field: vd ['content', 'data.body', 'blocks.html']
     */
    public function asyncMoveFileContent(array $newParams = [], array $oldParams = [], $arrField = [])
    {
        if (empty($arrField)) return;

        $allNew = [];
        $allOld = [];

        foreach ($arrField as $field) {
            // Lấy tất cả file ảnh từ HTML mới/cũ theo field (hỗ trợ dot notation)
            $newFiles = $this->extractImagesByField($newParams, $field);
            $oldFiles = $this->extractImagesByField($oldParams, $field);

            $allNew = array_merge($allNew, $newFiles);
            $allOld = array_merge($allOld, $oldFiles);
        }

        // Làm sạch & unique
        $allNew = array_values(array_filter(array_unique($allNew)));
        $allOld = array_values(array_filter(array_unique($allOld)));

        if (!empty($allNew)) {
            // move(new, old) -> chỉ move phần thật sự mới so với old
            return $this->move($allNew, $allOld);
        }
        return;
    }

    /**
     * So sánh giá trị theo key (file path thường) theo các field (kể cả 'data.files'),
     * rồi move các file mới xuất hiện (dispatch 1 lần).
     *
     * @param array $newParams
     * @param array $oldParams
     * @param array $arrField  Danh sách field: vd ['avatar', 'gallery', 'data.files']
     */
    public function asyncMoveFileKey(array $newParams = [], array $oldParams = [], $arrField = [])
    {
        if (empty($arrField)) return;

        $allNew = [];
        $allOld = [];

        foreach ($arrField as $field) {
            $newFiles = $this->extractFilesByField($newParams, $field);
            $oldFiles = $this->extractFilesByField($oldParams, $field);

            $allNew = array_merge($allNew, $newFiles);
            $allOld = array_merge($allOld, $oldFiles);
        }

        // Làm sạch & unique
        $allNew = array_values(array_filter(array_unique($allNew)));
        $allOld = array_values(array_filter(array_unique($allOld)));
        if (empty($allNew) && empty($allOld)) return;
        return $this->move($allNew, $allOld);
    }

    /* =================== Helpers =================== */

    /**
     * Lấy tất cả file theo field (hỗ trợ dot notation và mảng lồng),
     * dùng cho key path thông thường (không phải HTML).
     */
    private function extractFilesByField(array $source, string $field): array
    {
        $raws = $this->getValuesByField($source, $field);
        $files = [];
        foreach ($raws as $raw) {
            $files = array_merge($files, $this->normalizeFiles($raw));
        }
        // bỏ rỗng + trùng
        return array_values(array_filter(array_unique($files)));
    }

    /**
     * Lấy tất cả file ảnh từ HTML theo field (hỗ trợ dot notation và mảng lồng).
     */
    private function extractImagesByField(array $source, string $field, bool $onlyTmpOrSrc = false): array
    {
        $raws = $this->getValuesByField($source, $field);
        $files = [];
        foreach ($raws as $raw) {
            $files = array_merge($files, $this->extractImageFiles($raw, $onlyTmpOrSrc));
        }
        // bỏ rỗng + trùng
        return array_values(array_filter(array_unique($files)));
    }


    /**
     * Trả về các "giá trị thô" lấy theo field (hỗ trợ "a.b", và mảng nhiều item).
     * Caller sẽ tự chuẩn hoá tiếp.
     */
    private function getValuesByField(array $source, string $field): array
    {
        if (strpos($field, '.') === false) {
            return array_key_exists($field, $source) ? [$source[$field]] : [];
        }

        [$root, $sub] = explode('.', $field, 2);
        if (!array_key_exists($root, $source)) return [];

        $val = $source[$root];
        $out = [];

        // Trường hợp object/assoc có key con
        if (is_array($val) && array_key_exists($sub, $val)) {
            $out[] = $val[$sub];
            return $out;
        }

        // Trường hợp là danh sách item
        if (is_array($val)) {
            foreach ($val as $item) {
                if (!is_array($item)) continue;
                if (array_key_exists($sub, $item)) {
                    $out[] = $item[$sub];
                }
            }
        }
        return $out;
    }

    /**
     * Chuẩn hoá về mảng file phẳng cho key path thường.
     */
    private function normalizeFiles($value): array
    {
        if ($value === null) return [];

        if (is_string($value)) {
            $v = trim($value);
            return $v === '' ? [] : [$v];
        }

        if (is_array($value)) {
            $out = [];
            foreach ($value as $v) {
                if (is_array($v)) {
                    $out = array_merge($out, $this->normalizeFiles($v));
                } elseif (is_string($v)) {
                    $v = trim($v);
                    if ($v !== '') $out[] = $v;
                }
            }
            return $out;
        }

        return [];
    }

    /**
     * Trích danh sách ảnh (src) trong HTML hoặc mảng HTML.
     */
    private function extractImageFiles($input, bool $onlyTmpOrSrc = false): array
    {
        if (empty($input)) return [];

        $result = [];

        if (is_array($input)) {
            foreach ($input as $item) {
                if ($item === null || $item === '') continue;
                $result = [...$result, ...$this->extractImageFiles($item, $onlyTmpOrSrc)];
            }
            return array_values(array_unique($result));
        }

        if (!is_string($input)) return [];

        if (preg_match_all('/<img[^>]*\s+src=["\']([^"\']+)["\']/iu', $input, $m)) {
            foreach ($m[1] as $src) {
                $src = trim($src);
                if ($src === '') continue;
                if ($onlyTmpOrSrc && !str_contains($src, '/tmp') && !str_contains($src, '/src')) continue;
                $result[] = $src;
            }
        }

        return array_values(array_unique($result));
    }
}
