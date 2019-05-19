**Install:**
1. git clone https://github.com/petrokulybaba/products_catalog.git
2. git checkout dev
3. composer install
4. php bin/console d:d:c
5. php bin/console d:m:m (php bin/console d:s:u -f)
6. php bin/console d:f:l -n
7. php bin/console s:r

**Сonsole command to create a super admin:** php bin/console create:user (email) (password)

**Login as user (ROLE_USER):** user@mail.com 11111111

**Login as manager (ROLE_ADMIN_MANAGER):** manager@mail.com 11111111

**Login as super admin (ROLE_SUPER_ADMIN):** superadmin@mail.com 11111111

**Cron:**
1. crontab -e
2. Add: @daily php /(path to the project directory)/bin/console send:new-products
3. Save
4. In .env or .env.local set variable **ADMIN_EMAIL**

**API:**
1. Link on Postman collection: https://www.getpostman.com/collections/951f3b490ed20f8a476d
2. In Postman: File - Import - Import From Link
