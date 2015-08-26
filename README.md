KIS
===

KIS stands for keep it secret

The idea of the project is to keep some text documents in the most safest way. Even if anyone get access to the config file and the database they can not read the data unless they get their hands on the password of the file owners.

Please keep in mind the idea of the project is just to protect the documents from being read but it's your responsibility to prevent situation like lost data or changed data.

One more thing if all the users that a document is shared with forget their password there is no way to recover the content of the document.

And last but not least don't forget this project is not production ready and it's just a 2 days made project so use it with caution!

How to install
==============

Installing kis is pretty simple

```bash
composer install
app/console doctrine:database:create
app/console doctrine:schema:update --force
```

If you are planning to use it in production mode do not forget to use
```bash
app/console assetic:dump
```

To register a user go to /register

TODOs
=====
Make a better interface(I'm a backend developer :D)
Make a template for register