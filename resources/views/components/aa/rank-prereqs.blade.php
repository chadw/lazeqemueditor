@php
    $existingCount = count($rank['prereqs'] ?? []);
@endphp
<div class="mt-4" x-data="{ newRows: [] }">
    <div class="flex items-center justify-between mb-2">
        <h3 class="font-bold text-lg">
            Prerequisites
        </h3>
        <div class="flex items-center gap-2">
            <button type="button" class="btn btn-xs btn-soft btn-success"
                @click="newRows.push({ aa_id: '', points: '' })">
                <x-ui.icon name="add" /> Add Prereq
            </button>
        </div>
    </div>

    <div class="border border-base-content/5 bg-base-100">
        <table class="table table-sm table-zebra w-full">
            <thead class="text-xs uppercase bg-neutral">
                <tr>
                    <th>AA</th>
                    <th class="w-[5%]">Points</th>
                    <th class="w-[5%]"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rank['prereqs'] as $req)
                    <tr>
                        <td>
                            <div
                                x-data='ajaxSelect({
                                    searchUrl: "/aa/search",
                                    useModal: false,
                                    prefillValue: @json([
                                        "id" => $req->aa_id,
                                        "name" => $req->ability->name
                                    ]),
                                    keyInOption: true,
                                })'
                                x-init="init()"
                            >
                                <select
                                    x-ref="select"
                                    name="aa_id[]"
                                    class="w-full"
                                ></select>
                            </div>
                        </td>
                        <td>
                            <input name="points[]" value="{{ $req->points }}" class="input" />
                        </td>
                        <td class="text-right">
                            <form method="POST" action="{{ url("aa/ranks/{$rank['id']}/prereqs/{$req->aa_id}") }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-soft btn-error">
                                    <x-ui.icon name="delete" />
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                <template x-for="(nr, nidx) in newRows" :key="nidx">
                    <tr>
                        <td>
                            <div
                                x-data='ajaxSelect({
                                    searchUrl: "/aa/search",
                                    useModal: false,
                                    prefillValue: null,
                                    allowNone: false,
                                    noneId: -1,
                                    noneLabel: "Select an AA",
                                    keyInOption: true,
                                })'
                                x-init="init()"
                            >
                                <select
                                    x-ref="select"
                                    name="aa_id[]"
                                    class="w-full"
                                ></select>
                            </div>
                        </td>
                        <td>
                            <input name="points[]" x-model="nr.points" class="input" />
                        </td>
                        <td class="text-right">
                            <button type="button" class="btn btn-soft btn-error"
                                @click="newRows.splice(nidx,1)">
                                <x-ui.icon name="delete" />
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
