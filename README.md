### 프로젝트 개요
- 해당 프로젝트는 php, laravel 복습 목적으로, rest api 강의를 수강하면서 구현합니다.
- 강의내용에서 나아가, 일부 코드는 개선하며 작성합니다.

### 버전정보
- php 8.0
- laravel 8
- mysql 8.0

### 프로젝트 소스 다운로드 후 초기 설정
1. .env파일 생성 (.env.local 복사하여 사용)
2. 컴포저 의존성 설치
    ```
    $ composer install
    ```
3. 개발환경이 구축된 도커 컨테이너 실행
    ```
    $ ./vendor/bin/sail up
    ```
   => 이 때부터 웹 서버(내장서버) 실행되어 0.0.0.0:80으로 접근가능
4. db 스키마 적용
    ```
    $ sail bash
    /var/www/html# php artisan migrate
    ```
5. (디버깅툴 telescope 사용하는 경우) telescope assets 퍼블리시
    ```
    /var/www/html# php artisan telescope:publish
    ```
   => {app_url}/telescope 경로로 접속하여 대시보드 조회 가능
