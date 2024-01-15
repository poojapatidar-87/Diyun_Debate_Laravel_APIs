<!DOCTYPE html>
<html>
<head>
    <title>Team Invitation Mail</title>
</head>
<body>
<p>Hello,</p>

<p>You have been invited to join the team "{{ $teamName }}".</p>

<p>Click the following link to accept the invitation:</p>

<a href="{{ $invitationLink }}">{{ $invitationLink }}</a>

<p>Thank you!</p>
</body>
</html>
