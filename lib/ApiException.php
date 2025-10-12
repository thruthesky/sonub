<?php

/**
 * API 에러를 위한 커스텀 Exception 클래스
 *
 * 모든 API 함수에서 에러 발생 시 이 Exception을 throw합니다.
 * api.php에서 이 Exception을 catch하여 JSON 에러 응답으로 변환합니다.
 *
 * @example 기본 사용법
 * ```php
 * if ($user_id === null) {
 *     throw new ApiException('invalid-user-id', '사용자 ID가 필요합니다.');
 * }
 * ```
 *
 * @example HTTP 상태 코드 지정
 * ```php
 * if (!$user) {
 *     throw new ApiException('user-not-found', '사용자를 찾을 수 없습니다.', [], 404);
 * }
 * ```
 *
 * @example 추가 데이터 포함
 * ```php
 * throw new ApiException(
 *     'validation-failed',
 *     '입력값 검증 실패',
 *     ['field' => 'email', 'value' => $email],
 *     400
 * );
 * ```
 */
class ApiException extends Exception
{
    /**
     * 에러 코드 (kebab-case 형식)
     * @var string
     */
    private string $error_code;

    /**
     * 에러 메시지 (사용자에게 표시될 메시지)
     * @var string
     */
    private string $error_message;

    /**
     * 추가 에러 데이터
     * @var array
     */
    private array $error_data;

    /**
     * HTTP 응답 코드
     * @var int
     */
    private int $error_response_code;

    /**
     * ApiException 생성자
     *
     * @param string $code 에러 코드 (예: 'user-not-found', 'invalid-input')
     * @param string $message 에러 메시지 (사용자에게 표시될 메시지)
     * @param array $data 추가 에러 데이터 (선택사항)
     * @param int $response_code HTTP 응답 코드 (기본값: 400)
     */
    public function __construct(
        string $code = 'unknown',
        string $message = '',
        array $data = [],
        int $response_code = 400
    ) {
        // Exception 생성자 호출 (부모 클래스)
        parent::__construct($message, $response_code);

        // 에러 정보 저장
        $this->error_code = $code;
        $this->error_message = $message;
        $this->error_data = $data;
        $this->error_response_code = $response_code;
    }

    /**
     * 에러 정보를 배열로 변환
     *
     * api.php에서 JSON 응답을 생성할 때 사용합니다.
     *
     * @return array 에러 정보 배열
     */
    public function toArray(): array
    {
        return [
            'error_code' => $this->error_code,
            'error_message' => $this->error_message,
            'error_data' => $this->error_data,
            'error_response_code' => $this->error_response_code,
        ];
    }

    /**
     * 에러 코드 가져오기
     *
     * @return string 에러 코드
     */
    public function getErrorCode(): string
    {
        return $this->error_code;
    }

    /**
     * 에러 메시지 가져오기
     *
     * @return string 에러 메시지
     */
    public function getErrorMessage(): string
    {
        return $this->error_message;
    }

    /**
     * 에러 데이터 가져오기
     *
     * @return array 에러 데이터
     */
    public function getErrorData(): array
    {
        return $this->error_data;
    }

    /**
     * HTTP 응답 코드 가져오기
     *
     * @return int HTTP 응답 코드
     */
    public function getErrorResponseCode(): int
    {
        return $this->error_response_code;
    }
}
