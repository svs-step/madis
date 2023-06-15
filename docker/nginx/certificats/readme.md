## SSL

In order to use the application with a self-signed certificate you will need to install [mkcert](https://github.com/FiloSottile/mkcert) on your machine.  
Then build certificates for your local env 

```shell
mkcert -key-file key.pem -cert-file cert.pem 127.0.0.1 madis.local 51.255.50.113
```

and copy `cert.pem` and `key.pem` into this folder.
