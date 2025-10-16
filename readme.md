# ApiDomainer

ApiDomainer powers a robot battle league: send two lists of robot IDs, watch the platform assemble teams automatically, run a dance-off, and record the champion. The end result shows how to keep real-world business logic clean and testable while still shipping features quickly.

## ⚡ Core Highlights
- **Robot Dance-Off Engine** – POST two squads of robot IDs and the domain layer forms the teams, validates participants, runs the match-up, and persists the winner.
- **Clean CQRS Messaging** – Read/write concerns split across Symfony Messenger buses; queries return optimized payloads while commands orchestrate domain workflows.
- **View-Backed Read Models** – Doctrine maps read-only projections via attributes (`config/packages/doctrine.yaml`’s `AppView`) so we can hydrate DTO-like objects straight from SQL views.
- **API Platform Ready** – Resources, filters, and responders wire the domain to HTTP without leaking framework details into the core logic.
- **Developer-Friendly Separation** – Domain, Application, Infrastructure, and Action layers keep responsibilities sharp and testing approachable.

## 🚀 Quick Start
```bash
make build / make up
make install
```
Visit the 🌐 [API endpoint](http://localhost:8085/api)

## 🧭 Architecture at a Glance
- **Domain** – Entities (`Robot`, `Team`, `RobotDanceOff`), repositories, and services such as `RobotService`, plus value objects like `DanceOffTeams` that keep the domain framework-agnostic.
- **Application** – Query handlers (e.g., `GetRobotDanceOffQueryHandler`) and orchestration logic that coordinate domain services via Symfony Messenger.
- **Infrastructure** – Doctrine repositories, query builders, API Platform filters, and request DTOs. Handlers translate transport objects into domain value objects before delegating.
- **Action / Responder** – ADR-style actions act as controllers and responders turn domain models into API responses.

### Read Models & CQRS
- Write-side commands persist canonical aggregates (`RobotDanceOff`, `Team`, `Robot`).
- Read-side queries hydrate `RobotBattleView` objects from the `robot_battle_view` SQL view; Doctrine treats the view namespace (`App\\Domain\\ReadModel`) as attribute-mapped entities declared read-only.
- API Platform filters and orderers target these projections, keeping HTTP responses decoupled from the write models while still using Doctrine’s metadata and hydration pipeline.

## 🔬 Feature Flow: Creating a Dance-Off
1. **Request** – `POST /api/robots/dance-off` accepts a `RobotDanceOffRequest` with two arrays of robot IDs.
2. **Handler** – `RobotDanceOffHandler` converts the request into the `DanceOffTeams` value object.
3. **Domain Service** – `RobotService` validates each robot, assembles two `Team` aggregates, runs the experience-based scoring algorithm, and persists the resulting `RobotDanceOff`.
4. **Persistence & Response** – Teams and dance-off entities are saved via Doctrine repositories. The responder layer presents a structured payload when queried.

### Explore the League
- `GET /api/robots` – Browse all registered robots, filter by name, sort by experience, or inspect their stats individually.
- `GET /api/robots/dance-offs` – List dance-offs with search and ordering support, perfect for replaying past battles.
- `GET /api/robots/{id}` – Fetch a single competitor and see if they are ready for the next matchup.

Each read endpoint rides the query bus for separation of concerns and returns serialized responses through dedicated responders.

## 🧱 Tech Stack
- PHP 8.2, Symfony Messenger, API Platform, Doctrine ORM
- Docker + Makefile for repeatable environments, Apache reverse proxy fronting the PHP-FPM container
- PHPUnit with lightweight stubs for fast feedback

## 🤝 Why It Matters
This project demonstrates how production-grade patterns (DDD, CQRS, ADR) can stay approachable. You can trace a feature end-to-end—from HTTP request through domain logic—without wading through framework noise.
