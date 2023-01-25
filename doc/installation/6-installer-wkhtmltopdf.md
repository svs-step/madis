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
Il faut d'abord vérifier la version de l'OS du serveur :
```bash
tail /etc/os-release
```
Résultat :
```bash
PRETTY_NAME="Debian GNU/Linux 10 (buster)"
NAME="Debian GNU/Linux"
VERSION_ID="10"
VERSION="10 (buster)"
VERSION_CODENAME=buster
ID=debian
HOME_URL="https://www.debian.org/"
SUPPORT_URL="https://www.debian.org/support"
BUG_REPORT_URL="https://bugs.debian.org/"
```
La version dans l'exemple ci-dessus est donc "buster".
Se rendre alors sur https://wkhtmltopdf.org/downloads.html afin de sélectionner la bonne version de wkhtmltopdf pour celle-ci.
On choisira le binaire pour amd64 correspondant à la version de l'OS.
###### Note : La version de wkhtmltopdf actuellement utilisée par l'application n'étant pas la dernière mais la 0.12.5, il faut alors se rendre sur https://github.com/wkhtmltopdf/wkhtmltopdf/releases/0.12.5/ afin de sélectionner le bon binaire.


Une fois le binaire sélectionné, copier le lien de téléchargement et télécharger-le via wget :
```bash
wget https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.5/wkhtmltox_0.12.5-1.buster_amd64.deb
```

Puis, installer les prérequis de wkhtmltopdf pour fonctionner :
```bash
apt-get install fontconfig libfreetype6 libjpeg62-turbo libpng16-16 libxrender1 xfonts-75dpi xfonts-base
```

Enfin, installer le binaire:
```bash
dpkg -i wkhtmltox_0.12.5-1.buster_amd64.deb
```

Vérifier alors la bonne configuration des variables d'envrionnement suivantes dans le .env, et
 modifier les paths si nécessaire :
```
WKHTMLTOPDF_PATH=/usr/local/bin/wkhtmltopdf
WKHTMLTOIMAGE_PATH=/usr/local/bin/wkhtmltoimage
```
