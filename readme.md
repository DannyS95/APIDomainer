# ApiDomainer
A Domain-Driven API Platform Framework with ADR structure.

---

## 🚀 Features:
- 🗂️ **Domain-Driven Design (DDD)** principles.
- 🎯 **Action-Domain-Responder (ADR)** architecture.
- 🔄 **Command Bus Pattern** for clean write operations.
- 🔍 **API Platform Integration** with custom serialization.
- 📦 **Dockerized environment** for easy setup and scalability.

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
make install
docker compose up -d
