<?php


class T
{

    public $texts = TEXTS;
    private array $params = [];

    /**
     * 동적으로 언어 번역 파일을 로드
     *
     * @param string $name 로드할 파일명 (확장자 제외)
     * @return T 체이닝을 위한 자기 자신 반환
     *
     * 사용법:
     * - t()->load('company') : etc/translations/company.php 파일 로드
     * - t()->load('admin')->관리자 : admin.php 로드 후 '관리자' 키 번역
     */
    public function load(string $name): T
    {
        $file_path = __DIR__ . '/' . $name . '.texts.php';

        // 파일이 존재하는지 확인
        if (file_exists($file_path)) {
            // 파일에서 TEXTS 배열 가져오기
            $loaded_texts = include_once $file_path;

            // 배열인지 확인하고 병합
            if (is_array($loaded_texts)) {
                // 기존 texts 배열에 새로운 번역 추가 (덮어쓰기)
                $this->texts = array_merge($this->texts, $loaded_texts);
            }
        }

        return $this;
    }

    /**
     * 부분 번역 파일을 로드하여 배열로 반환
     *
     * @param string $name 로드할 파일명 (확장자 제외)
     * @return array TEXTS 배열 반환, 파일이 없거나 배열이 아니면 빈 배열 반환
     *
     * 사용법:
     * - t()->read('company') : etc/translations/company.php 파일 로드 후 TEXTS 배열 반환
     */
    public function read(string $name): array
    {
        $file_path = __DIR__ . '/' . $name . '.php';

        // 파일이 존재하는지 확인
        if (file_exists($file_path)) {
            // 파일에서 TEXTS 배열 가져오기
            $loaded_texts = include $file_path;
            return is_array($loaded_texts) ? $loaded_texts : [];
        }
        return [];
    }

    public function params(array $params): T
    {
        $this->params = $params;
        return $this;
    }

    public function __get($name)
    {
        if (isset($this->texts[$name])) {
            $result = tr($this->texts[$name], $this->params);
            $this->params = [];
            return $result;
        }
        return $name;
    }

    /**
     * 배열로 텍스트 주입
     * 
     * @param array $texts 키-값 쌍의 배열
     * @return T 체이닝을 위한 자기 자신 반환
     * 
     * 사용법:
     * - t()->inject(['key1' => 'value1', 'key2' => 'value2'])
     */
    public function inject($texts)
    {
        if (is_array($texts)) {
            $this->texts = array_merge($this->texts, $texts);
        }
        return $this;
    }
}

function t(): T
{
    static $t = null;
    if ($t === null) {
        $t = new T();
    }
    return $t;
}

/**
 * TEXTS 배열에 $key 에 해당하는 텍스트가 있는지 확인
 * 
 * 용도:
 * - 번역된 텍스트가 있는지 확인해서, 있으면 출력하고, 없으면 출력하지 않는 경우에 사용
 * 
 * @param string $key 번역 키
 * @return bool 텍스트
 */
function has_text(string $key): bool
{
    return isset(TEXTS[$key]);
}
