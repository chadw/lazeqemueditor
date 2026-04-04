@extends('layouts.app')
@section('title', 'Mail')
@section('page-title', 'Mail')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('mail.store') }}',
                    resourceName: 'Mail'
                })">
                <x-ui.icon name="add" /> New Mail
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[20%]">From</th>
                    <th scope="col" class="w-[20%]">To</th>
                    <th scope="col">Subject</th>
                    <th scope="col" class="w-[10%]">Sent</th>
                    <th scope="col" class="w-[10%] md:table-cell text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($mails as $mail)
                    @php
                        $mail->datetime = \Carbon\Carbon::parse($mail->timestamp)->format('Y-m-d H:i');
                        $mail->friendlyStatus = config('everquest.mail_status')[$mail->status];
                    @endphp
                    <tr x-data data-mail='@json($mail)'>
                        <td>{{ $mail->from }}</td>
                        <td>{{ $mail->character->name }}</td>
                        <td>{{ $mail->subject }}</td>
                        <td>
                            {{ $mail->timestamp
                                ? \Carbon\Carbon::parse($mail->timestamp)->format('Y-m-d H:i')
                                : '' }}
                        </td>
                        <td class="text-right">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft btn-accent tooltip"
                                    data-tip="Show"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.mail,
                                        '{{ route('mail.update', $mail) }}',
                                        { resourceName: 'Mail Display' }
                                    )">
                                    <x-ui.icon name="show" />
                                </button>
                                <form action="{{ route('mail.destroy', $mail) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                        data-tip="Delete"
                                        onclick="return confirm('Delete?')">
                                        <x-ui.icon name="delete" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-ui.table>

        <div class="mt-4">{{ $mails->links() }}</div>

        <x-modal-show title="Mail Details">
            @include('mail.partials.form')
        </x-modal-show>
    </div>
@endsection
