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
                if (!isset($params['preview'])) $params['preview'] = 1;
                $input = array_merge($params, ['path' => $path]);
                return "{$this->url}/api/files/show?" . http_build_query($input);
            case "r2":
                return env('SERVICE_MEDIA_URL_R2', '') . '/' . trim($path, '/');
            default:
                return env('SERVICE_MEDIA_URL', '') . '/' . trim($path, '/');
        }
    }
    private function getFileNameFromPath(string $path): string
    {
        if (!strpos($path, 'path=') === false) {
            $path = array_reverse(explode('path=', $path))[0] ?? '';
            $path = strtok($path, '&'); // hoặc: $path = explode('&', $path)[0];
        }
        // bỏ khoảng trắng, nháy, v.v.
        $path = trim($path, " \t\n\r\0\x0B'\"");
        // decode nếu dạng %encoded
        if (strpos($path, '%') !== false) {
            $path = urldecode($path);
        }
        return trim($path, "/");
    }

    public function moveDownload($datas = [], $channel = '', $folder = '')
    {
        $fileNews = $fileDownload = $fileReturn = [];
        foreach ($datas as $file) {
            if (is_array($file)) {
                $file = array_map(function ($item) {
                    return $this->getFileNameFromPath($item);
                }, $file);
                $fileNews = array_merge($fileNews, array_values($file));
            } else {
                $fileNews[] = $this->getFileNameFromPath($file);
            }
        }

        if (empty($fileNews)) return [];
        foreach ($fileNews as $k => $item) {
            if (strpos($file, '/tmp') === 0  || strpos($file, 'tmp')  === 0) {
                $path = str_replace(['//'], '/', "{$channel}/{$folder}/" . date('Y/m/d') . '/' . basename($item));
                $fileDownload[] = [
                    'url' => $item,
                    'path' => $path
                ];
                $fileReturn[] = $path;
                unset($fileNews[$k]);
            } else {
                $fileReturn[] = $item;
            }
        }

        if (empty($fileNews) && empty($fileDownload)) return; 
        \Microservices\Jobs\BusJob::dispatch($this->_listener, [
            'files' => array_values($fileNews),
            'download' => $fileDownload
        ])->onQueue($this->_service_code);
        return $fileReturn;
    }

    public function move($datas = [], $dataOlds = [], $channel = '')
    {
        $fileNews = $fileOlds = [];
        foreach ($datas as $file) {
            if (is_array($file)) {
                $file = array_map(function ($item) {
                    return $this->getFileNameFromPath($item);
                }, $file);
                $fileNews = array_merge($fileNews, array_values($file));
            } else {
                $fileNews[] = $this->getFileNameFromPath($file);
            }
        }
        if (!empty($dataOlds)) {

            foreach ($dataOlds as $file) {
                if (is_array($file)) {
                    $old = array_map(function ($item) {
                        return $this->getFileNameFromPath($item);
                    }, $file);
                    $fileOlds = array_merge($fileOlds, array_values($old));
                } else {
                    $fileOlds[] = $this->getFileNameFromPath($file);
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
    public function convertPathSave($params, $fields, bool $handelDomain = false, $option = []): array
    {
        if (!is_array($params) || empty($fields)) return (array) $params;

        foreach ($fields as $path) {
            $this->applyTransformByPath($params, $path, function ($val) use ($handelDomain, $option) {
                return $this->replaceTmpToSrcRecursive($val, $handelDomain, $option);
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
    private function replaceTmpToSrcRecursive($data, bool $handelDomain = false, $option = [])
    {
        // String
        if (is_string($data)) {
            $str = trim($data);

            if (stripos($str, '<img') !== false) {
                // sửa trong HTML (src, srcset)
                $str = $this->replaceTmpInHtmlImages($str, $handelDomain, $option);
                return $str;
            }
            // ❗ Không phải đường dẫn file → return luôn, không động vào
            if (!$this->isFilePath($str)) {
                return $str;
            }
            $path = $this->normalizePathString($str);
            return !empty($handelDomain) ? $this->replaceDomainInHtml($path, $option) :  $path;
        }

        // Array
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->replaceTmpToSrcRecursive($v, $handelDomain, $option);
            }
        }

        return $data;
    }

    private function isFilePath(string $str): bool
    {
        return preg_match('/\.(jpg|jpeg|png|gif|webp|webm|svg|pdf|docx?|xlsx?|pptx?|zip|mp4|m4a|mp3|wav|ogg|flac|aac|opus)$/i', $str);
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
    private function replaceTmpInHtmlImages(string $html, $isDomain = true, $option = []): string
    {

        // src="..."/src='...'/src=unquoted...
        $html = preg_replace_callback(
            '/\bsrc\s*=\s*(["\']?)([^"\'>\s]+)\1/iu',
            function ($m) use ($isDomain, $option) {
                $url  = $m[2];
                $url = array_reverse(explode('path=', $url))[0] ?? '';
                if ((preg_match('#^https?://#i',  $url) || preg_match('#^http?://#i',  $url)) && !$this->isAllowedEnvUrl($url)) {
                    return str_replace($m[2], $url, $m[0]);
                }
                $new  = $this->normalizePathString($url);
                if (!empty($isDomain)) $new = $this->replaceDomainInHtml($new, $option);
                return str_replace($m[2], $new, $m[0]);
            },
            $html
        );
        return $html;
    }

    private function replaceDomainInHtml(string $url, $option = []): string
    {
        if (empty($url)) return '';
        $url = trim(html_entity_decode($url));
        // URL tuyệt đối ngoài hệ thống thì giữ nguyên
        if ((preg_match('#^https?://#i', $url) || preg_match('#^http?://#i', $url)) && !$this->isAllowedEnvUrl($url)) {
            return $url;
        }
        $option = collect($option)->only(['preview'])->toArray();
        return $this->show($url, $option);
    }


    /**
     * So sánh giá trị theo key (file path thường) theo các field (kể cả 'data.files'),
     * rồi move các file mới xuất hiện (dispatch 1 lần).
     *
     * @param array $newParams
     * @param array $oldParams
     * @param array $arrField  Danh sách field: vd ['avatar', 'gallery', 'data.files']
     */

    public function asyncMoveFileKey(array $newParams = [], array $oldParams = [], $arrField = [], $asyncDelete = true)
    {
        if (empty($arrField)) return;
        $arrDataNew = $arrDataOld = $allNew = $allOld = [];
        foreach ($arrField as $field) {
            $newValues = $this->getDotByField($newParams, $field);
            $oldValues = $this->getDotByField($oldParams, $field);
            // Nếu new KHÔNG chứa field → coi như không update → bỏ qua xoá
            if (!empty($newValues)) $arrDataNew = array_merge($arrDataNew, $newValues);
            if (!empty($oldValues)) $arrDataOld = array_merge($arrDataOld, $oldValues);
        }

        if (!empty($arrDataNew)) foreach ($arrDataNew as $item) {
            $allNew = array_merge($allNew, $this->normalizeFiles($item));
        }
        if (!empty($arrDataOld)) foreach ($arrDataOld as $item) {
            $allOld = array_merge($allOld, $this->normalizeFiles($item));
        }

        $diffNew = array_diff($allNew, $allOld); // NEW có, OLD không
        $diffOld = array_diff($allOld, $allNew); // OLD có, NEW không

        if (empty($diffNew) && empty($diffOld)) return;
        if (empty($asyncDelete)) $diffOld = [];
        return $this->move($diffNew, $diffOld);
    }



    private function buildFieldRegex(string $field): string
    {
        $parts = explode('.', $field);
        $last  = count($parts) - 1;

        $regex = '/^';
        foreach ($parts as $i => $part) {
            $regex .= preg_quote($part, '/');

            if ($i < $last) {
                $regex .= '(?:\.\d+)?\.';
            }
        }

        $regex .= '(?:\.\d+)?$/';
        return $regex;
    }

    private function getDotByField(array $source, string $field): array
    {
        $dot = \Arr::dot($source);
        $results = [];
        $pattern = $this->buildFieldRegex($field);

        foreach ($dot as $key => $value) {
            if (preg_match($pattern, (string) $key)) {
                $results[] = $value;
            }
        }

        return $results;
    }

    /**
     * Chuẩn hoá về mảng file phẳng cho key path thường.
     */
    private function normalizeFiles($arrValue = []): array
    {
        if (empty($arrValue)) return [];

        if (is_string($arrValue)) {
            $str = trim($arrValue);
            if (stripos($str, '<img') !== false) {
                // sửa trong HTML (src, srcset)
                $arr = $this->extractImageFiles($str, true);
                return $arr;
            }
            // ❗ Không phải đường dẫn file → return luôn, không động vào
            if (!$this->isFilePath($str)) return [];
            return array($str);
        }
        $newFiles = [];
        if (is_array($arrValue)) {
            foreach ($arrValue as $value) {
                if (is_string($value)) {
                    $str = trim($value);
                    if (stripos($str, '<img') !== false) {
                        // sửa trong HTML (src, srcset)
                        $arr = $this->extractImageFiles($str, true);
                        $newFiles = array_merge($newFiles, $arr);
                        continue;
                    }
                    // ❗ Không phải đường dẫn file → return luôn, không động vào
                    if (!$this->isFilePath($str)) continue;
                    $newFiles[] = $str;
                }
            }
        }
        return $newFiles;
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
                $domainImg = $this->getDomainFromUrl($src);
                if (!in_array($domainImg, $this->domains)) continue;
                $result[] = str_replace($this->domains, '', $src);
            }
        }
        return array_values(array_unique($result));
    }
    private function getDomainFromUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') return '';

        // Trường hợp dạng //example.com/path
        if (strpos($url, '//') === 0) {
            $url = 'http:' . $url;
        }

        $parsed = parse_url($url);

        // ❗ Không có host → URL tương đối → không có domain
        if (empty($parsed['host'])) {
            return ''; // hoặc return null; tùy bạn
        }

        $domain = ($parsed['scheme'] ?? 'http') . '://' . $parsed['host'];

        if (!empty($parsed['port'])) {
            $domain .= ':' . $parsed['port'];
        }

        return $domain;
    }
}
