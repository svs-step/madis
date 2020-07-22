## Hooks git

#### 1. Copy .git-hooks directory into project

#### 2. Deplace the hooks to the git's hook repository
Execute this command from your local at the root directory of your project
```bash
cp -R .git-hooks/hooks/* .git/hooks
chmod -R 744 .git/hooks
```

#### 3. Change the git's hook path configuration
Execute this command from your local at the root directory of your project
```bash
git config core.hooksPath .git/hooks
```
This command tell to git where he haves to check for hooks.
By default the hooks path is in .git/hooks, but maybe in future other hooks will be create.
