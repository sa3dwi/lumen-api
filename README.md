# Lumen Api

This demo used Lumen5.5 to make REST api for user with jwt authentication, posts and comments.


## USAGE

```
$ git clone git@github.com:sa3dwi/lumen-api.git
$ composer install
$ cp .env.example .env
$ vim .env
        DB_*
            config  uration your database
	    JWT_SECRET
            php artisan jwt:secret
	    APP_KEY
            key:generate is abandoned in lumen, so do it yourself
            md5(uniqid())，str_random(32) etc.，maybe use jwt:secret and copy it

$ php artisan migrate
$ php artisan db:seed
$ generate api doc like this "apidoc -i App/Http/Controller/Api/v1 -o public/apidoc"
```

## REST API DESIGN

just a demo for rest api design

```
    get    /api/posts              	 post index
    post   /api/posts              	 create a post
    get    /api/posts/1            	 post detail
    put    /api/posts/1           	 replace a post
    patch  /api/posts/1            	 update part of a post
    delete /api/posts/1            	 delete a post
    get    /api/posts/1/comments         comment list of a post
    post   /api/posts/1/comments         add a comment
    get    /api/posts/1/comments/5       comment detail
    put    /api/posts/1/comments/5       replace a comment
    patch  /api/posts/1/comments/5       update part of a comment
    delete /api/posts/1/comments/5       delete a comment
    get    /api/users/3/posts            post list of a user
    get    /api/user/posts               post list of current user
```


## TODO

- [ ] adding phpunit tests

## Contribute to the development

This project is a 100% free and open source project. The source code is publicly available on GitHub.com. If you want to contribute to the development, simply fork the project, hack the code and send pull requests with your updates.
