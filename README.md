# Slack
## Installation
以下の環境変数をセットする必要があります:

```sh
export SLACK_INCOMING_WEB_HOOK_API_URL='hoge'
export SLACK_USERNAME='fuga'
export SLACK_ICON_EMOJI='piyo'
```

## Usage
`post-as-bot --help`を見てください

```shell-session
$ slack post-as-bot -t 'hoge'

$ who | slack post-as-bot
```
