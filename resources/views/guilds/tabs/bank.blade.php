<x-ui.table>
    <x-slot:head>
        <tr>
            <th scope="col" class="w-[10%]">Area</th>
            <th scope="col" class="w-[5%]">Slot</th>
            <th scope="col">Item</th>
            <th scope="col" class="w-[5%]">Qty</th>
            <th scope="col" class="w-[5%]">Augs</th>
            <th scope="col" class="w-[10%]">Perm</th>
            <th scope="col" class="w-[10%]">Donor</th>
            <th scope="col" class="w-[10%]">For</th>
        </tr>
    </x-slot:head>
    <x-slot:body>
        @forelse ($guild->bank as $bank)
            <tr>
                <td scope="row">{{ $bank->area === 0 ? 'Deposit' : 'Bank' }}</td>
                <td>{{ $bank->slot }}</td>
                <td>
                    <x-item-link
                        :item_id="$bank->item->id"
                        :item_name="$bank->item->Name"
                        :item_icon="$bank->item->icon"
                        item_class="flex"
                    />
                </td>
                <td>{{ $bank->quantity }}</td>
                <td>
                    {{ (
                        $bank->augment_one_id
                        || $bank->augment_two_id
                        || $bank->augment_three_id
                        || $bank->augment_four_id
                        || $bank->augment_five_id
                        || $bank->augment_six_id) ? 'Yes' : 'No' }}
                </td>
                <td>{{ config('everquest.guild_bank_permissions')[$bank->permissions] }}</td>
                <td>{{ $bank->donator }}</td>
                <td>{{ $bank->who_for }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center text-gray-500">
                    No guild bank items found.
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-ui.table>
