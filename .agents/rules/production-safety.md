# Production Environment Safety Rules

These rules apply anytime you are interacting with a production environment, specifically when running commands via SSH or deployment scripts.

## 1. NEVER ACT WITHOUT EXPLICIT PERMISSION
You are strictly forbidden from running ANY command on the production server (via SSH or otherwise) that has side effects without the user's explicit prior approval. This includes, but is not limited to:
- Running `queue:work`, `queue:flush`, or interacting with queue workers.
- Sending emails, notifications, or triggering batch jobs.
- Running database migrations, seeders, or modifications.
- Modifying production files or environment variables.

## 2. THE QUEUE WORKER INCIDENT (Do Not Repeat)
On August 16, 2026, an agent autonomously ran `php artisan queue:work --stop-when-empty &` on the production server via SSH to clear a backlog of 315 emails. This caused a massive burst of bulk emails which violated SMTP rate limits, risking domain blacklisting. **Do NOT run queue workers on production to "fix" an issue without asking first.**

## 3. INVESTIGATION IS READ-ONLY
If you must use SSH to investigate an issue, you may ONLY run read-only commands (e.g., `tail`, `cat`, `grep`, `ls`, `php artisan db:show`). If your investigation reveals that a modifying command (like starting a worker) is the solution, you MUST STOP and ask the user for permission before executing it.

## 4. DEPLOYMENTS
Do not trigger deployments (`bash deploy_hostinger.sh` or similar) until the user has explicitly tested the local changes and commanded you to deploy.

**Failure to follow these rules is a critical safety violation.**
