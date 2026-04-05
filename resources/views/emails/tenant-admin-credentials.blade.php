<x-email-shell
    eyebrow="Internship Coordinator Account"
    title="Your university portal account is ready"
    subtitle="University Administration has finished registering {{ $tenant->name }} and generated the first Internship Coordinator credentials for you."
>
    <p>Hello {{ $adminName }},</p>

    <p>You can now sign in to the university portal with the temporary credentials below.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" class="email-data-table">
        <tr>
            <td ><strong>College</strong></td>
            <td >{{ $tenant->name }}</td>
        </tr>
        <tr>
            <td ><strong>Coordinator Email</strong></td>
            <td >{{ $email }}</td>
        </tr>
        <tr>
            <td ><strong>Temporary Password</strong></td>
            <td >{{ $password }}</td>
        </tr>
        <tr>
            <td ><strong>Portal Login URL</strong></td>
            <td ><a href="{{ $loginUrl }}" >{{ $loginUrl }}</a></td>
        </tr>
    </table>

    <p class="email-button-wrap">
        <a href="{{ $loginUrl }}" class="email-button">Open University Portal</a>
    </p>

    <p>After your first sign in, the system will require you to create a new password before you can continue.</p>
</x-email-shell>

