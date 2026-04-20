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
        #auth : FirebaseAuth
        #firestore : FirestoreDatabase
        +showLogin() View
        +login(Request request) Redirect
        +logout() Redirect
        +showForgotPassword() View
        +sendPasswordResetEmail(Request request) Redirect
        +showResetPassword(Request request) View
        +resetPassword(Request request) Redirect
    }

    class DashboardController {
        #firestore : FirestoreDatabase
        #kalbarDaerahList()$ array
        #normalizeToKalbarDaerah(string raw) string
        +index(Request request) View
    }

    class ArticleController {
        #firestore : FirestoreDatabase
        #storage : FirebaseStorage
        -getArticlesList() array
        +index() View
        +downloadList(Request request) StreamedResponse
        +create() View
        +store(Request request) Redirect
        +edit(string id) View
        +update(Request request, string id) Redirect
        +destroy(string id) Redirect
    }

    class LaporanController {
        #firestore : FirestoreDatabase
        #storage : FirebaseStorage
        #kategoriMap : array
        -createdDateToLocal(mixed value) string
        -getLaporanList() array
        -findReportRefById(string id) array
        +index() View
        +downloadList(Request request) StreamedResponse
        +detail(string id) View
        +setStatus(Request request, string id) Response
        +destroy(string id) Redirect
        +downloadPDF(string id) Response
        +chat(string id) View
        +chatMessages(string id) JsonResponse
        +sendChat(Request request, string id) JsonResponse
        +deleteChat(string id, string messageId) JsonResponse
        +updateChat(Request request, string id, string messageId) JsonResponse
        +unreadChats() JsonResponse
        +markChatRead(string id) JsonResponse
    }

    class PengaturanController {
        #auth : FirebaseAuth
        #firestore : FirestoreDatabase
        +index() View
        +formTambahAdmin() View
        +tambahAdmin(Request request) Redirect
        +hapusAdmin(string uid) Redirect
    }

    class ProfileController {
        #auth : FirebaseAuth
        #firestore : FirestoreDatabase
        +index() View
        +updatePassword(Request request) Redirect
    }

    Controller <|-- AuthController
    Controller <|-- DashboardController
    Controller <|-- ArticleController
    Controller <|-- LaporanController
    Controller <|-- PengaturanController
    Controller <|-- ProfileController
```

---

## 2. Class Diagram — Entitas Data (Firestore Collections)

```mermaid
classDiagram
    direction TB

    class Admin {
        +uid : string
        +email : string
        +role : string
        +created_at : string
    }

    class FirebaseUser {
        +uid : string
        +name : string
        +email : string
    }

    class Article {
        +id : string
        +title : string
        +articleType : string
        +description : string
        +photoUrl : string
        +gsUrl : string
        +releasedDate : string
        +updateDate : string
    }

    class Report {
        +id : string
        +report_number : string
        +case_type : string
        +report_status : string
        +user_name : string
        +user_id : string
        +phone_number : string
        +child_age : string
        +incident_city : string
        +incident_location : string
        +incident_date : string
        +detail_description : string
        +created_date : number
        +adminLastReadAt : number
    }

    class ChatMessage {
        +chatId : string
        +textMessage : string
        +imageMessage : string
        +userId : string
        +chatType : string
        +reportId : string
        +messageStatus : string
        +isDeleted : boolean
        +createdAt : number
        +lastActionAt : number
        +dayMessage : string
    }

    FirebaseUser "1" --> "0..*" Report : membuat
    Report "1" *-- "0..*" ChatMessage : memiliki
    Admin "1" --> "0..*" ChatMessage : mengirim pesan
    FirebaseUser "1" --> "0..*" ChatMessage : mengirim pesan
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
    ArticleController --> Article : CRUD
    LaporanController --> Report : CRUD
    LaporanController --> ChatMessage : CRUD
    PengaturanController --> Admin : CRUD
    ProfileController --> Admin : membaca & update password
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
        #auth : FirebaseAuth
        #firestore : FirestoreDatabase
        +showLogin() View
        +login(Request) Redirect
        +logout() Redirect
        +showForgotPassword() View
        +sendPasswordResetEmail(Request) Redirect
        +showResetPassword(Request) View
        +resetPassword(Request) Redirect
    }

    class DashboardController {
        #firestore : FirestoreDatabase
        #kalbarDaerahList()$ array
        #normalizeToKalbarDaerah(string) string
        +index(Request) View
    }

    class ArticleController {
        #firestore : FirestoreDatabase
        #storage : FirebaseStorage
        -getArticlesList() array
        +index() View
        +downloadList(Request) StreamedResponse
        +create() View
        +store(Request) Redirect
        +edit(string) View
        +update(Request, string) Redirect
        +destroy(string) Redirect
    }

    class LaporanController {
        #firestore : FirestoreDatabase
        #storage : FirebaseStorage
        #kategoriMap : array
        -createdDateToLocal(mixed) string
        -getLaporanList() array
        -findReportRefById(string) array
        +index() View
        +downloadList(Request) StreamedResponse
        +detail(string) View
        +setStatus(Request, string) Response
        +destroy(string) Redirect
        +downloadPDF(string) Response
        +chat(string) View
        +chatMessages(string) JsonResponse
        +sendChat(Request, string) JsonResponse
        +deleteChat(string, string) JsonResponse
        +updateChat(Request, string, string) JsonResponse
        +unreadChats() JsonResponse
        +markChatRead(string) JsonResponse
    }

    class PengaturanController {
        #auth : FirebaseAuth
        #firestore : FirestoreDatabase
        +index() View
        +formTambahAdmin() View
        +tambahAdmin(Request) Redirect
        +hapusAdmin(string) Redirect
    }

    class ProfileController {
        #auth : FirebaseAuth
        #firestore : FirestoreDatabase
        +index() View
        +updatePassword(Request) Redirect
    }

    %% ============ DATA ENTITIES ============
    class Admin {
        +uid : string
        +email : string
        +role : string
        +created_at : string
    }

    class FirebaseUser {
        +uid : string
        +name : string
        +email : string
    }

    class Article {
        +id : string
        +title : string
        +articleType : string
        +description : string
        +photoUrl : string
        +gsUrl : string
        +releasedDate : string
        +updateDate : string
    }

    class Report {
        +id : string
        +report_number : string
        +case_type : string
        +report_status : string
        +user_name : string
        +user_id : string
        +phone_number : string
        +child_age : string
        +incident_city : string
        +incident_location : string
        +incident_date : string
        +detail_description : string
        +created_date : number
        +adminLastReadAt : number
    }

    class ChatMessage {
        +chatId : string
        +textMessage : string
        +imageMessage : string
        +userId : string
        +chatType : string
        +reportId : string
        +messageStatus : string
        +isDeleted : boolean
        +createdAt : number
        +lastActionAt : number
        +dayMessage : string
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
    ArticleController --> Article : CRUD
    LaporanController --> Report : CRUD
    LaporanController --> ChatMessage : CRUD
    PengaturanController --> Admin : CRUD
    ProfileController --> Admin : reads

    %% ============ ENTITY RELATIONSHIPS ============
    FirebaseUser "1" --> "0..*" Report : membuat
    Report "1" *-- "0..*" ChatMessage : memiliki
```

---

## Keterangan Simbol

| Simbol | Arti |
|---|---|
| `+` | public |
| `#` | protected |
| `-` | private |
| `$` | static |
| `<\|--` | inheritance (pewarisan) |
| `-->` | dependency / association (menggunakan) |
| `*--` | composition (bagian yang tidak bisa berdiri sendiri) |
| `"1" → "0..*"` | multiplicity (1 ke banyak) |

## Ringkasan Relasi Antar Entitas

| Relasi | Tipe | Keterangan |
|---|---|---|
| FirebaseUser → Report | **One to Many** | 1 user bisa membuat banyak laporan |
| Report → ChatMessage | **Composition** | 1 laporan memiliki banyak pesan chat (chat tidak ada tanpa report) |
| Admin → ChatMessage | **One to Many** | Admin bisa mengirim pesan ke banyak chat |

## Ringkasan Controller → Entitas

| Controller | Entitas yang diakses | Operasi |
|---|---|---|
| **AuthController** | Admin | Read, Validate login |
| **DashboardController** | FirebaseUser, Article, Report | Read, Agregasi statistik |
| **ArticleController** | Article | Create, Read, Update, Delete |
| **LaporanController** | Report, ChatMessage | Create, Read, Update, Delete |
| **PengaturanController** | Admin | Create, Read, Delete |
| **ProfileController** | Admin | Read, Update password |
