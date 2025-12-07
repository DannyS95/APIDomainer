# ApiDomainer

A friendly API for running a robot dance-off league: send two sets of robot IDs, the platform assembles the teams, runs the matchup, and tracks the winner. Perfect for showcasing clean API flows to newcomers or recruiters without diving into internals.

## What You Can Do
- Start a new dance-off between two squads of robots.
- Replay an existing battle with a couple of roster swaps.
- Browse all robots and their stats.
- Check a scoreboard of recent battles and winners.

## Key API Routes
- `POST /api/robots/dance-off` – Start a fresh battle with two lists of robot IDs.
- `POST /api/robot-battles/replays` – Replay a past battle with up to two swaps per side.
- `GET /api/robot-battles` – Scoreboard of battles and their latest results.
- `GET /api/robots` – Browse robots, filter, and sort.
- `GET /api/robots/{id}` – View a single robot.

## Run Locally
```bash
make build
make install
make up
```
Then visit http://localhost:8085/api
