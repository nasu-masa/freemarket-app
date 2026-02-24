<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>メールアドレスの認証</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 24px;">

    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 8px;">

        <h1 style="font-size: 24px; color: #333333; margin-bottom: 24px;">
            {{ $user->name }} さん、メールアドレスの認証をお願いします
        </h1>

        <p style="color: #555555; line-height: 1.8; margin-bottom: 24px;">
            この度はご登録いただきありがとうございます。<br>
            下記のボタンをクリックして、メールアドレスの認証を完了してください。
        </p>

        <div style="text-align: center; margin: 40px 0;">
            <a href="{{ $verificationUrl }}"
                style="display: inline-block; padding: 12px 32px; background-color: #e53935; color: #ffffff;
                    text-decoration: none; border-radius: 6px; font-size: 16px;">
                メール認証を完了する
            </a>
        </div>

        <p style="color: #777777; font-size: 14px; line-height: 1.6;">
            ※ このリンクは 60 分で有効期限が切れます。<br>
            ※ このメールに心当たりがない場合は破棄してください。
        </p>

        <hr style="margin: 32px 0; border: none; border-top: 1px solid #e0e0e0;">

        <p style="color: #999999; font-size: 12px;">
            このメールは送信専用です。返信いただいてもお答えできません。
        </p>

    </div>

</body>

</html>