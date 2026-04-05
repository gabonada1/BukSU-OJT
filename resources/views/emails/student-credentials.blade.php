<x-email-shell
    eyebrow="Student Account"
    title="Your student account is ready"
    subtitle="An Internship Coordinator created your practicum account in {{ $tenant->name }} and issued a temporary password."
>
    <p>Hello {{ $student->full_name }},</p>

    <p>You can sign in right away using the credentials below.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" class="email-data-table">
        <tr>
            <td ><strong>College</strong></td>
            <td >{{ $tenant->name }}</td>
        </tr>
        <tr>
            <td ><strong>Email / Username</strong></td>
            <td >{{ $student->email }}</td>
        </tr>
        <tr>
            <td ><strong>Temporary Password</strong></td>
            <td >{{ $plainPassword }}</td>
        </tr>
        <tr>
            <td ><strong>Portal Login URL</strong></td>
            <td ><a href="{{ $loginUrl }}" >{{ $loginUrl }}</a></td>
        </tr>
    </table>

    <p class="email-button-wrap">
        <a href="{{ $loginUrl }}" class="email-button">Open Student Portal</a>
    </p>

    <p>Your account has already been verified by the Internship Coordinator, so you can sign in immediately.</p>
</x-email-shell>

