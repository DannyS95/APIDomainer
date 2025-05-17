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

# 🤖 **Robot Flow Explanation**

---

## 🚀 **Overview**
The **Robot Flow** is part of the **Robot API** responsible for managing:
- The creation of **Robot DanceOffs**.
- Building **Teams** dynamically based on provided Robot IDs.
- Performing **DanceOffs** between two teams.
- Selecting a **winner** if the logic requires it.

The flow is built around **DDD (Domain-Driven Design)** and follows **ADR (Action-Domain-Responder)** principles for clean separation of logic.

---

# 🤖 **DanceOff Workflow Explanation**

---

## 🚀 **Overview**
The **DanceOff Workflow** is the central logic for orchestrating competitive matchups between two robot teams within the **Robot API**.  
It leverages **Domain-Driven Design (DDD)** principles and follows **ADR (Action-Domain-Responder)** architecture to keep business logic isolated and maintainable.

---

# 🤖 **Team Formation and Winner Selection Workflow for Robot Battles API**

---

## 🚀 **Overview**
The **Team Formation and Winner Selection Workflow** is part of the `DanceOff` creation logic.  
It follows DDD principles to:
1. Create **Team Alpha** and **Team Beta** based on provided Robot IDs.
2. Register the teams into a `DanceOff`.
3. Manage the **winner selection** logic when a DanceOff is concluded.

---

## 🔄 **Workflow Breakdown**
### **1️⃣ Team Formation**
When a `POST` request is made to:

## 🔄 **Team Formation Workflow**

---

### **Steps:**
1. The request payload contains two arrays of `Robot IDs`:
   - **teamA:** Represents the IDs for **Team Alpha**.
   - **teamB:** Represents the IDs for **Team Omega**.

2. The request is handled by the `RobotDanceOffHandler`, which triggers the `RobotService`.

3. Inside `RobotService`, the `DanceOffFactory` is called to:
   - Create two new instances of `Team`.
   - Name them **Team Alpha** and **Team Omega**.
   - Fetch each `Robot` from the database using its ID.
   - Populate each `Team` with the fetched `Robot` entities.

4. If any of the Robot IDs are not found in the database:
   - An exception is thrown.
   - The DanceOff creation is aborted.

5. If all IDs are valid:
   - Both `Team Alpha` and `Team Omega` are constructed and ready for the `DanceOff`.

---

### **Example Payload:**  
```json
{
  "teamA": [1, 2, 3, 4, 5],
  "teamB": [6, 7, 8, 9, 10]
}
```


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

🌐 http://localhost:8080/api

