<script>
    export let users;
</script>

<table>
<tr>
 <th> Name </th>
 <th> Email </th>
</tr>
{#each users as {name, email}}
<tr>
 <td> {name} </td>
 <td> {email} </td>
</tr>
{/each}
</table>
