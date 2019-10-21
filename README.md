# ethereum-wallet-check

To run this app you need to:

* create .env in the root and change its content by replacing .env.dist structure with your values
* run `docker-compose up eth-mmysql` and check if `ethereum` database exists and contains 3 table. If not, apply a dump `/data/init/mysql/createSchema.sql`
* insert some "wallets" to `account` table like `INSERT INTO account (number) VALUES ('0xc02aaa39b223fe8d0a0e5c4f27ead9083c756cc2')` to make the app track them
* `ctrl+c` to shut down the mysql container
* `docker-compose up` to run the whole app

Set `DEBUG=true` in `.env` config to see logs of the app

### Example

![image](https://user-images.githubusercontent.com/5726656/67196137-aae1ee00-f413-11e9-95bd-26174181f13e.png)
