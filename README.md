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

4. db 스키마 적용 및 시딩
    ```
    $ ./vendor/bin/sail bash
    /var/www/html# php artisan migrate
    /var/www/html# php artisan db:seed
    ```
5. (디버깅툴 telescope 사용하는 경우) telescope assets 퍼블리시
    ```
    /var/www/html# php artisan telescope:publish
    # telescope테이블 생성 필요시 마이그레이션 실행
    /var/www/html# php artisan migrate 
    ```
    => {app_url}/telescope 경로로 접속하여 대시보드 조회 가능

6. passport인증을 위한 encryption key 및 클라이언트 생성
   ```
   # encryptiion key와 personal access 클라이언트, password grant 클라이언트 생성
   /var/www/html# php artisan passport:install 
   # client credentials 클라이언트 생성
   /var/www/html# php artisan passport:client --client 
   ```
   => 생성된 클라이언트의 id, secret은 token 발생시 사용할 수 있도록 보관

7. phpstorm 내장 http client 사용하여 테스트 
   1. tests/Http/http-client.example.env.json을 http-client.env.json이름으로 복사
   2. http-client.env.json파일에서 필요한 항목들 채워넣기 (6에서 생성한 클라이언트의 id, secret들을 해당하는 필드에 붙여넣어 사용)
   3. .http파일을 열고 상단에서 실행환경을 선택
   4. api request 실행
