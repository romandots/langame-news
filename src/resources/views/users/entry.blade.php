<tr class="{{ !$user->is_confirmed ? 'bg-gray-200 dark:bg-gray-800 text-gray-500 dark:text-gray-400' : 'bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100' }}">
    <td class="px-4 py-2">{{ $user->id }}</td>
    <td class="px-4 py-2">{{ $user->name }}</td>
    <td class="px-4 py-2">{{ $user->email }}</td>
    <td class="px-4 py-2">
        {{ $user->is_confirmed ? 'Да' : 'Нет' }}
    </td>
    <td class="px-4 py-2">{{ $user->confirmation_code }}</td>
    <td class="px-4 py-2">{{ $user->created_at }}</td>
</tr>
