# Instagram Tools
Created by Reza Juliandri.
Thank you to [mgp25](https://github.com/mgp25/Instagram-API) for instagram API.

## Requirements
- PHP V.7.2
- Composer
## How to use
Make sure you have installed php and composer in your machine.

Run this command in terminal where you clone this repository.
```bash
composer install
```
Composer will download requirements file for this tools.

Move env files and setting your env.
```
cp .env.example .env
nano .env
```

When all done you can start with
```bash
php index.php
```
You'll get new .json files(allFollowers.json, allFollowing.json, followers.json, following.json).
- allFollowers.json (All followers your target instagram)
- allFollowing.json (All following your target instagram)
- followers.json (All followers you not followback)
- following.json (Following not follback you)


When all .json file generated you can choose  what you want next.
```
php <filename>.php
```

Example:
    You want unfollow who not follow back you.

```
php unfollowNotFollback.php
```

Script will get following.json and start unfollow account in list.
