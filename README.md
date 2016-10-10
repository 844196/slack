# Slack
CLIからSlackへ投稿するやつ

```shell-session
$ slack post 'ラーメン食べたい'
$ slack post --username=メモおじさん --icon-emoji=:memo: --channel=#time_ojisan 'これはメモです'
$ tree | slack post --pre
```

## Installation
```shell-session
$ composer global config repositories.844196/slack git https://github.com/844196/slack
$ composer global require 844196/slack
$ export PATH="$HOME/.composer/vendor/bin:$PATH"
```

## Setup
以下の環境変数をセットする必要があります:

```sh
export SLACK_API_TOKEN='XXXXXX-XXXXXXX-XXXXXXXXXXXX'
export SLACK_DEFAULT_CHANNEL='#time_844196'
```
