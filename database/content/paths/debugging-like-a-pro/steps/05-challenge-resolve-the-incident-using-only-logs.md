---json
{
    "order": 5,
    "title": "Challenge: Resolve the Incident Using Only Logs",
    "type": "challenge",
    "description": "In production you do not have Xdebug. You have logs. A payment system is failing silently — some payments are processed, others are not, and users only find out hours later. You have received a 48-hour log dump. Find the pattern, identify the root cause and propose the fix.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": "resolve-incident-from-logs",
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": "The CFO called. 127 payments in the last 24 hours were marked as \"pending\" but the gateway already processed and charged the customer. The money left the user's account, but the order was never confirmed. You have the logs. You have 1 hour to find the cause before the board meeting.",
    "resources": [
        {
            "url": "https://www.elastic.co/what-is/log-analysis",
            "label": "Log analysis techniques"
        },
        {
            "url": "https://www.digitalocean.com/community/tutorials/how-to-use-journalctl-to-view-and-manipulate-systemd-logs",
            "label": "grep and awk for log analysis"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Download the challenge log file (link in the materials)"
        },
        {
            "id": 2,
            "text": "Use grep/awk to filter only payment events with ERROR status"
        },
        {
            "id": 3,
            "text": "Identify the temporal pattern: does the bug happen always? Only at specific times?"
        },
        {
            "id": 4,
            "text": "Correlate error logs with context logs (user_id, request_id) to trace a complete request"
        },
        {
            "id": 5,
            "text": "Document your root cause hypothesis with evidence from the logs"
        },
        {
            "id": 6,
            "text": "Write a test that reproduces the identified failure scenario"
        }
    ],
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

