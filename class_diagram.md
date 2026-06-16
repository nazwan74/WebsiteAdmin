# Class Diagram — WebsiteAdmin

Class diagram lengkap untuk aplikasi **WebsiteAdmin** berbasis Laravel + Firebase.

---

## 1. Class Diagram — Controllers (Inheritance + Atribut + Method)

```mermaid
classDiagram
    direction TB

    class Controller {
        <<abstract>>
    }

    class AuthController {
        #FirebaseAuth auth
        #FirestoreDatabase firestore
        +showLogin() View
        +login(request) Redirect
        +logout() Redirect
        +showForgotPassword() View
        +sendPasswordResetEmail(request) Redirect
        +showResetPassword(request) View
        +resetPassword(request) Redirect
    }

    class DashboardController {
        #FirestoreDatabase firestore
        #kalbarDaerahList() array
        #normalizeToKalbarDaerah(raw) string
        +normalizeStatus(status) string
        +index(request) View
        +refresh() Redirect
        +saveSettings(request) JsonResponse
        +resetSettings() JsonResponse
    }

    class ArticleController {
        #FirestoreDatabase firestore
        #FirebaseStorage storage
        #array allowedTypes
        -parseArticleDate(value) string
        -getArticlesList() array
        +index() View
        +refresh() Redirect
        +downloadList(request) StreamedResponse
        +create() View
        +store(request) Redirect
        +edit(id) View
        +update(request, id) Redirect
        +destroy(id) Redirect
        +bulkDestroy(request) Redirect
    }

    class LaporanController {
        #FirestoreDatabase firestore
        #FirebaseStorage storage
        #array kategoriMap
        -createdDateToLocal(value) string
        -getLaporanList() array
        -findReportRefById(id) array
        +index() View
        +refresh() Redirect
        +downloadList(request) StreamedResponse
        +detail(id) View
        +setStatus(request, id) Response
        +destroy(id) Response
        +downloadPDF(id) Response
        +chat(id) View
        +chatMessages(id) JsonResponse
        +sendChat(request, id) JsonResponse
        +deleteChat(id, messageId) JsonResponse
        +updateChat(request, messageId) JsonResponse
        +unreadChats() JsonResponse
        +markChatRead(id) JsonResponse
    }

    class PengaturanController {
        #FirebaseAuth auth
        #FirestoreDatabase firestore
        +index() View
        +formTambahAdmin() View
        +tambahAdmin(request) Redirect
        +hapusAdmin(uid) Redirect
    }

    class ProfileController {
        #FirebaseAuth auth
        #FirestoreDatabase firestore
        +index() View
        +updatePassword(request) Redirect
    }

    Controller <|-- AuthController
    Controller <|-- DashboardController
    Controller <|-- ArticleController
    Controller <|-- LaporanController
    Controller <|-- PengaturanController
    Controller <|-- ProfileController
```

---

## 2. Class Diagram — Entitas Data (Firestore Collections & Subcollections)

```mermaid
classDiagram
    direction TB

    class Admin {
        +string uid
        +string email
        +string role
        +string created_at
        +array dashboard_settings
    }

    class FirebaseUser {
        +string uid
        +string name
        +string email
    }

    class Article {
        +string id
        +string title
        +string articleType
        +string description
        +string photoUrl
        +string gsUrl
        +number releasedDate
        +number updateDate
    }

    class Report {
        +string id
        +string report_number
        +string case_type
        +string report_status
        +string user_name
        +string user_id
        +string phone_number
        +string child_age
        +string incident_city
        +string incident_location
        +string incident_date
        +string detail_description
        +number created_date
        +number adminLastReadAt
        +number lastMessageAt
        +string lastMessageText
        +string docPath
    }

    class ChatMessage {
        +string chatId
        +string textMessage
        +string imageMessage
        +string imagePath
        +string userId
        +string chatType
        +string reportId
        +string messageStatus
        +boolean isDeleted
        +number createdAt
        +number lastActionAt
        +string dayMessage
    }

    FirebaseUser "1" --> "0..*" Report : membuat
    Report "1" *-- "0..*" ChatMessage : memiliki
    Admin "1" --> "0..*" ChatMessage : mengirim pesan
    FirebaseUser "1" --> "0..*" ChatMessage : mengirim pesan
    Admin "1" --> "0..*" Report : mengelola
    Admin "1" --> "0..*" Article : mengelola
    FirebaseUser "0..*" --> "0..*" Article : membaca
```

---

## 3. Class Diagram — Relasi Controller ↔ Entitas Data

```mermaid
classDiagram
    direction TB

    class AuthController
    class DashboardController
    class ArticleController
    class LaporanController
    class PengaturanController
    class ProfileController

    class Admin
    class FirebaseUser
    class Article
    class Report
    class ChatMessage

    AuthController --> Admin : membaca & memvalidasi
    DashboardController --> FirebaseUser : membaca jumlah
    DashboardController --> Article : membaca jumlah
    DashboardController --> Report : membaca & agregasi
    DashboardController --> Admin : membaca & update dashboard_settings
    ArticleController --> Article : CRUD
    LaporanController --> Report : CRUD
    LaporanController --> ChatMessage : CRUD
    PengaturanController --> Admin : CRUD
    ProfileController --> Admin : membaca
```

---

## 4. Class Diagram Lengkap (Gabungan)

```mermaid
classDiagram
    direction TB

    %% ============ BASE ============
    class Controller {
        <<abstract>>
    }

    %% ============ CONTROLLERS ============
    class AuthController {
        #FirebaseAuth auth
        #FirestoreDatabase firestore
        +showLogin() View
        +login(request) Redirect
        +logout() Redirect
        +showForgotPassword() View
        +sendPasswordResetEmail(request) Redirect
        +showResetPassword(request) View
        +resetPassword(request) Redirect
    }

    class DashboardController {
        #FirestoreDatabase firestore
        #kalbarDaerahList() array
        #normalizeToKalbarDaerah(raw) string
        +normalizeStatus(status) string
        +index(request) View
        +refresh() Redirect
        +saveSettings(request) JsonResponse
        +resetSettings() JsonResponse
    }

    class ArticleController {
        #FirestoreDatabase firestore
        #FirebaseStorage storage
        #array allowedTypes
        -parseArticleDate(value) string
        -getArticlesList() array
        +index() View
        +refresh() Redirect
        +downloadList(request) StreamedResponse
        +create() View
        +store(request) Redirect
        +edit(id) View
        +update(request, id) Redirect
        +destroy(id) Redirect
        +bulkDestroy(request) Redirect
    }

    class LaporanController {
        #FirestoreDatabase firestore
        #FirebaseStorage storage
        #array kategoriMap
        -createdDateToLocal(value) string
        -getLaporanList() array
        -findReportRefById(id) array
        +index() View
        +refresh() Redirect
        +downloadList(request) StreamedResponse
        +detail(id) View
        +setStatus(request, id) Response
        +destroy(id) Response
        +downloadPDF(id) Response
        +chat(id) View
        +chatMessages(id) JsonResponse
        +sendChat(request, id) JsonResponse
        +deleteChat(id, messageId) JsonResponse
        +updateChat(request, messageId) JsonResponse
        +unreadChats() JsonResponse
        +markChatRead(id) JsonResponse
    }

    class PengaturanController {
        #FirebaseAuth auth
        #FirestoreDatabase firestore
        +index() View
        +formTambahAdmin() View
        +tambahAdmin(request) Redirect
        +hapusAdmin(uid) Redirect
    }

    class ProfileController {
        #FirebaseAuth auth
        #FirestoreDatabase firestore
        +index() View
        +updatePassword(request) Redirect
    }

    %% ============ DATA ENTITIES ============
    class Admin {
        +string uid
        +string email
        +string role
        +string created_at
        +array dashboard_settings
    }

    class FirebaseUser {
        +string uid
        +string name
        +string email
    }

    class Article {
        +string id
        +string title
        +string articleType
        +string description
        +string photoUrl
        +string gsUrl
        +number releasedDate
        +number updateDate
    }

    class Report {
        +string id
        +string report_number
        +string case_type
        +string report_status
        +string user_name
        +string user_id
        +string phone_number
        +string child_age
        +string incident_city
        +string incident_location
        +string incident_date
        +string detail_description
        +number created_date
        +number adminLastReadAt
        +number lastMessageAt
        +string lastMessageText
        +string docPath
    }

    class ChatMessage {
        +string chatId
        +string textMessage
        +string imageMessage
        +string imagePath
        +string userId
        +string chatType
        +string reportId
        +string messageStatus
        +boolean isDeleted
        +number createdAt
        +number lastActionAt
        +string dayMessage
    }

    %% ============ INHERITANCE ============
    Controller <|-- AuthController
    Controller <|-- DashboardController
    Controller <|-- ArticleController
    Controller <|-- LaporanController
    Controller <|-- PengaturanController
    Controller <|-- ProfileController

    %% ============ CONTROLLER → ENTITY ============
    AuthController --> Admin : reads
    DashboardController --> FirebaseUser : reads
    DashboardController --> Article : reads
    DashboardController --> Report : reads
    DashboardController --> Admin : reads & updates
    ArticleController --> Article : CRUD
    LaporanController --> Report : CRUD
    LaporanController --> ChatMessage : CRUD
    PengaturanController --> Admin : CRUD
    ProfileController --> Admin : reads

    %% ============ ENTITY RELATIONSHIPS ============
    FirebaseUser "1" --> "0..*" Report : membuat
    Report "1" *-- "0..*" ChatMessage : memiliki
    Admin "1" --> "0..*" ChatMessage : mengirim pesan
    FirebaseUser "1" --> "0..*" ChatMessage : mengirim pesan
    Admin "1" --> "0..*" Report : mengelola
    Admin "1" --> "0..*" Article : mengelola
    FirebaseUser "0..*" --> "0..*" Article : membaca
```

---

## Keterangan Simbol

| Simbol | Arti |
|---|---|
| `+` | public |
| `#` | protected |
| `-` | private |
| `$` | static |
| `<|--` | inheritance (pewarisan) |
| `-->` | dependency / association (menggunakan) |
| `*--` | composition (bagian yang tidak bisa berdiri sendiri) |
| `"1" → "0..*"` | multiplicity (1 ke banyak) |

## Ringkasan Relasi Antar Entitas

| Relasi | Tipe | Keterangan |
|---|---|---|
| FirebaseUser → Report | **One to Many** | 1 user bisa membuat banyak laporan |
| Report → ChatMessage | **Composition** | 1 laporan memiliki banyak pesan chat (chat tidak ada tanpa report) |
| Admin → ChatMessage | **One to Many** | Admin bisa mengirim pesan ke banyak chat |
| FirebaseUser → ChatMessage | **One to Many** | User bisa mengirim pesan ke banyak chat |
| Admin → Report | **One to Many (Logis)** | Admin mengelola banyak laporan kasus |
| Admin → Article | **One to Many (Logis)** | Admin mengelola banyak artikel edukasi |
| FirebaseUser → Article | **Many to Many (Logis)** | Banyak user membaca banyak artikel |

## Ringkasan Controller → Entitas

| Controller | Entitas yang diakses | Operasi |
|---|---|---|
| **AuthController** | Admin | Read, Validate login |
| **DashboardController** | FirebaseUser, Article, Report, Admin | Read, Agregasi statistik, Update settings |
| **ArticleController** | Article | Create, Read, Update, Delete |
| **LaporanController** | Report, ChatMessage | Create, Read, Update, Delete |
| **PengaturanController** | Admin | Create, Read, Delete |
| **ProfileController** | Admin | Read |
