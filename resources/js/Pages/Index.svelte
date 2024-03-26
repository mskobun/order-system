<script>
    import { inertia, Link } from '@inertiajs/svelte'
    export let title;
</script>

<div>
    <h1>{title}</h1>
    <Link href="/users">Users</Link>
</div>
