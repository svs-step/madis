Installation de WKHTMLTOPDF
============================
Afin de permettre la génération des PDFs par l'application, l'installation du binaire wkhtmltopdf 
est nécessaire.

## Environnement de développement

Le téléchargement, l'installation et le paramétrage de wkhtmltopdf est déjà prête à l'emploi lors
de l'installation de l'application en environnement de développement:
- Le Dockerfile télécharge le binaire
- Il installe ensuite les prérequis pour le binaire
- Il décompile wkhtmltopdf
- La configuration du bundle KnpSnappyBundle se charge de la configuration via les variables d'environnement (.env)

## Environnement de production
Il est nécessaire, pour l'environnement de production, de télécharger manuellement wkhtmltopdf.
La version utilisée actuellement par l'application est la version 0.12.5.
```bash
wget https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.5/wkhtmltox_0.12.5-1.buster_amd64.deb
```

Puis, installer les prérequis de wkhtmltopdf pour fonctionner :
```bash
apt-get install fontconfig libfreetype6 libjpeg62-turbo libpng16-16 libxrender1 xfonts-75dpi xfonts-base
```

Enfin, décompiler le binaire:
```bash
dpkg -i wkhtmltox_0.12.5-1.buster_amd64.deb
```

Vérifier alors la bonne configuration des variables d'envrionnement suivantes dans le .env, et
 modifier les paths si nécessaire :
```
WKHTMLTOPDF_PATH=/usr/local/bin/wkhtmltopdf
WKHTMLTOIMAGE_PATH=/usr/local/bin/wkhtmltoimage
```