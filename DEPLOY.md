# Auto Deploy

`main` branch-এ push করলেই app automatically `/var/www/automart_api`-তে deploy হবে
(PHP 8.2, port 8002)। কোনো manual command দরকার নেই।

## Shudhu ei Secret 4ta github-e add koro

GitHub repo → **Settings → Secrets and variables → Actions → New repository secret**

| Secret             | দরকার? | মান                                            |
|--------------------|--------|------------------------------------------------|
| `DEPLOY_HOST`      | হ্যাঁ  | server IP (যেমন `103.xx.xx.xx`)                |
| `DEPLOY_USER`      | হ্যাঁ  | `root`                                         |
| `DEPLOY_PASSWORD`  | হ্যাঁ  | root-এর password                               |
| `DEPLOY_SSH_PORT`  | না     | SSH port (না দিলে 22 হবে)                       |

তারপর ফাইল commit করো:

```
git add .
git commit -m "add auto deploy"
git push origin main
```

Deploy automatic শুরু হয়ে যাবে। `.env`, `vendor/`, uploads — এইগুলো কখনো মুছে যাবে না।
