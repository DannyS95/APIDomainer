# ApiDomainer
A Domain-Driven API Platform Framework with ADR structure.

---

## 🚀 Features:
- 🗂️ **Domain-Driven Design (DDD)** principles.
- 🎯 **Action-Domain-Responder (ADR)** architecture.
- 🔄 **Command Bus Pattern** for clean write operations.
- 🔍 **API Platform Integration** with custom serialization.
- 📦 **Dockerized environment** for easy setup and scalability.

## ⚙️ Project Architecture

This project follows a clean **DDD (Domain-Driven Design)** and **ADR (Action-Domain-Responder)** architecture with clear separation of concerns.

---

### **🏷️ Domain Layer**
**Path:** `src/Domain/Repository/`

The **Domain Layer** is responsible for:
- Defining entities (`Robot`, `RobotDanceOff`, etc.).
- Providing the repository interfaces (`RobotRepositoryInterface`, `RobotDanceOffRepositoryInterface`).
- Managing business logic related to fetching and persisting entities.
- Applying query logic by calling Infrastructure's `DoctrineRepository`.

---

### **🏷️ Infrastructure Layer**
**Path:** `src/Infrastructure/Repository/`

The **Infrastructure Layer** handles:
- Direct database communication with Doctrine.
- **Normalizers:**  
    - Custom normalizers are used to transform entities into the appropriate API response format.  
    - These are injected and mapped in `services.yaml` for API Platform serialization.

---

### **🏷️ Application Layer**
**Path:** `src/Application/Service/`

The **Application Layer** handles:
- Orchestration of calls to Domain Repositories.
- Business logic and service calls (e.g., bulk saves, custom queries).
- Manages transactions and interactions between multiple repositories.

---

### **🏷️ Action Layer (ADR Pattern)**
**Path:** `src/Action/`

The **Action Layer** is the controller endpoint for API requests:
- API Resources (configured with API Platform) map directly to Actions.
- Actions are decoupled from Symfony controllers and follow ADR principles.
- **Flow Example:**  
    - API Resource `/api/robots` → Calls `RobotCollectionAction`.  
    - Action retrieves request data and filters → Calls Domain Service.  
    - Domain Service → Calls the appropriate Repository.  
    - Repository → Calls `DoctrineRepository` for database operations.  
    - Response is sent back, optionally through a **Normalizer** for transformation.

---

### **🏷️ Flow Example**


---

## 🚀 Future Roadmap:
1️⃣ **Phase 1 → Command Bus Mastery**  
   - Focus on clean write operations using Symfony Messenger and API Platform.

2️⃣ **Phase 2 → Read Handlers Introduction**  
   - Introduce `Query` objects for read operations.
   - Experiment with Symfony Messenger for synchronous queries.

3️⃣ **Phase 3 → Full CQRS API**  
   - Separate Read and Write models entirely.
   - Implement optimized read handlers with dedicated query objects.


## ⚡️ Quick Start
To start the application, simply run:

```bash
docker compose up -d
make install
