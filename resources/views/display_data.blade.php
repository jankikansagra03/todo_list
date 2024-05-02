<table>
    @foreach ($result as $k)
        <tr>
            <td>
                {{ $k->fullname }}
            </td>
            <td>
                {{ $k->email }}
            </td>
            <td>
                {{ $k->password }}
            </td>
            <td>
                {{ $k->profile_pic }}
            </td>
            <td>
                <a href="{{ URL::to('/') }}/edit_profile/{{ $k->email }}">Edit</a>
            </td>
        </tr>
    @endforeach
</table>
