소너브 개발에 필요한 정보:
- 본 문서는 소너브 웹 개발 가이드라인이나 API 정보 및 프로젝트를 개발하는데 이해를 도와저는 정보를 제공하는 문서가 아닙니다.
- 오직, 개발에 필요한 서버, 계정, 툴, 라이브러리 등의 정보를 제공하는 문서입니다.



# 사용자 인증 및 로그인

## 임시 테스트 계정 정보

**모든 테스트 계정의 비밀번호**: `12345a,*`

### 테스트 계정 목록

| # | 과일 이름 | 이메일 | 전화번호 | 로그인 방법 |
|---|----------|--------|---------|-----------|
| 1 | 🍎 Apple | `apple@test.com` | `+11234567890` | `apple@test.com:12345a,*` 또는 `login_as('apple')` |
| 2 | 🍌 Banana | `banana@test.com` | `+11234567891` | `banana@test.com:12345a,*` 또는 `login_as('banana')` |
| 3 | 🍒 Cherry | `cherry@test.com` | `+11234567892` | `cherry@test.com:12345a,*` 또는 `login_as('cherry')` |
| 4 | 🍮 Durian | `durian@test.com` | `+11234567893` | `durian@test.com:12345a,*` 또는 `login_as('durian')` |
| 5 | Elderberry | `elderberry@test.com` | `+11234567894` | `elderberry@test.com:12345a,*` 또는 `login_as('elderberry')` |
| 6 | 🤎 Fig | `fig@test.com` | `+11234567895` | `fig@test.com:12345a,*` 또는 `login_as('fig')` |
| 7 | 🍇 Grape | `grape@test.com` | `+11234567896` | `grape@test.com:12345a,*` 또는 `login_as('grape')` |
| 8 | 🍈 Honeydew | `honeydew@test.com` | `+11234567897` | `honeydew@test.com:12345a,*` 또는 `login_as('honeydew')` |
| 9 | Jackfruit | `jackfruit@test.com` | `+11234567898` | `jackfruit@test.com:12345a,*` 또는 `login_as('jackfruit')` |
| 10 | 🥝 Kiwi | `kiwi@test.com` | `+11234567899` | `kiwi@test.com:12345a,*` 또는 `login_as('kiwi')` |
| 11 | 🍋 Lemon | `lemon@test.com` | `+11234567900` | `lemon@test.com:12345a,*` 또는 `login_as('lemon')` |
| 12 | 🥭 Mango | `mango@test.com` | `+11234567901` | `mango@test.com:12345a,*` 또는 `login_as('mango')` |

### 전화번호 초기화 SQL


- **테스트 계정 생성 쿼리**:

```sql
insert into users (firebase_uid, first_name, phone_number, created_at) values
('apple', 'Apple', '+11234567890', 1620000000)
on duplicate key update
  first_name = values(first_name),
  phone_number = values(phone_number);

insert into users (firebase_uid, first_name, phone_number, created_at) values
('banana', 'Banana', '+11234567891', 1620000000)
on duplicate key update
  first_name = values(first_name),
  phone_number = values(phone_number);

insert into users (firebase_uid, first_name, phone_number, created_at) values
('cherry', 'Cherry', '+11234567892', 1620000000)
on duplicate key update
  first_name = values(first_name),
  phone_number = values(phone_number);

insert into users (firebase_uid, first_name, phone_number, created_at) values
('durian', 'Durian', '+11234567893', 1620000000)
on duplicate key update
  first_name = values(first_name),
  phone_number = values(phone_number);

insert into users (firebase_uid, first_name, phone_number, created_at) values
('elderberry', 'Elderberry', '+11234567894', 1620000000)
on duplicate key update
  first_name = values(first_name),
  phone_number = values(phone_number);

insert into users (firebase_uid, first_name, phone_number, created_at) values
('fig', 'Fig', '+11234567895', 1620000000)
on duplicate key update
  first_name = values(first_name),
  phone_number = values(phone_number);

insert into users (firebase_uid, first_name, phone_number, created_at) values
('grape', 'Grape', '+11234567896', 1620000000)
on duplicate key update
  first_name = values(first_name),
  phone_number = values(phone_number);

insert into users (firebase_uid, first_name, phone_number, created_at) values
('honeydew', 'Honeydew', '+11234567897', 1620000000)
on duplicate key update
  first_name = values(first_name),
  phone_number = values(phone_number);

insert into users (firebase_uid, first_name, phone_number, created_at) values
('jackfruit', 'Jackfruit', '+11234567898', 1620000000)
on duplicate key update
  first_name = values(first_name),
  phone_number = values(phone_number);

insert into users (firebase_uid, first_name, phone_number, created_at) values
('kiwi', 'Kiwi', '+11234567899', 1620000000)
on duplicate key update
  first_name = values(first_name),
  phone_number = values(phone_number);

insert into users (firebase_uid, first_name, phone_number, created_at) values
('lemon', 'Lemon', '+11234567900', 1620000000)
on duplicate key update
  first_name = values(first_name),
  phone_number = values(phone_number);

insert into users (firebase_uid, first_name, phone_number, created_at) values
('mango', 'Mango', '+11234567901', 1620000000)
on duplicate key update
  first_name = values(first_name),
  phone_number = values(phone_number);
```


- **업데이트 쿼리**:

```sql
update users set phone_number='+11234567890' where firebase_uid='apple';
update users set phone_number='+11234567891' where firebase_uid='banana';
update users set phone_number='+11234567892' where firebase_uid='cherry';
update users set phone_number='+11234567893' where firebase_uid='durian';
update users set phone_number='+11234567894' where firebase_uid='elderberry';
update users set phone_number='+11234567895' where firebase_uid='fig';
update users set phone_number='+11234567896' where firebase_uid='grape';
update users set phone_number='+11234567897' where firebase_uid='honeydew';
update users set phone_number='+11234567898' where firebase_uid='jackfruit';
update users set phone_number='+11234567899' where firebase_uid='kiwi';
update users set phone_number='+11234567900' where firebase_uid='lemon';
update users set phone_number='+11234567901' where firebase_uid='mango';
```


### 로그인 방법 (3가지)

#### 1️⃣ 로그인 페이지에서 직접 입력
```
URL: https://local.sonub.com/user/login
입력: banana@test.com:12345a,*
→ SMS 인증 없이 즉시 로그인
```

#### 2️⃣ Dev Login 패널 (개발 환경에서만 표시)
```
1. 웹사이트 오른쪽 하단의 👤 아이콘 클릭
2. 드롭다운에서 과일 이름 선택 (예: Banana)
3. 즉시 로그인 완료
```

#### 3️⃣ JavaScript 콘솔 또는 테스트 코드
```javascript
// JavaScript 콘솔
login_as('banana');  // banana@test.com으로 로그인

// 또는 직접 호출
const user = await login_email_password('banana@test.com', '12345a,*');
await func('login_with_firebase', { firebase_uid: user.uid });
```