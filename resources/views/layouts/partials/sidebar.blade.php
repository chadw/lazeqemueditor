<aside class="bg-neutral/95 backdrop-blur w-64 shrink-0 h-screen overflow-y-auto border-r border-base-300">
    <div x-data class="border-b border-base-content/10 px-4 md:px-6 py-3.5 text-lg font-bold tracking-wide hidden lg:flex items-center justify-between">
        <span>LazEQEmu Editor</span>
        <button @click="$store.sidebar.toggleCollapse()" class="btn btn-ghost btn-sm btn-square" aria-label="Collapse sidebar">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
    </div>

    <nav class="px-3 py-6 space-y-2 text-sm mb-20">
        {{-- world data --}}
        <x-nav.sidebar-section
            title="World Data"
            section="world"
            :active="[
                'zones.*',
                'spawngroups.*',
                'tasks.*',
                'databuckets.*',
                'qglobals.*',
                'content-flags.*',
                'ldon-trap-templates.*',
            ]"
        >
            <x-slot name="icon">
                <x-ui.icon name="world" />
            </x-slot>
            <x-nav.sidebar-link route="zones.index" :active="['zones.index', 'zones.edit']">Zones</x-nav.sidebar-link>
            <x-nav.sidebar-link route="spawngroups.index" active="spawngroups.*">Spawn Groups</x-nav.sidebar-link>
            <x-nav.sidebar-link route="tasks.index" active="tasks.*">Tasks</x-nav.sidebar-link>
            <x-nav.sidebar-link route="zones.graveyards.index" active="zones.graveyards.*">
                Graveyards
            </x-nav.sidebar-link>
            <x-nav.sidebar-link route="databuckets.index">Data Buckets</x-nav.sidebar-link>
            <x-nav.sidebar-link route="qglobals.index">Quest Globals</x-nav.sidebar-link>
            <x-nav.sidebar-link route="content-flags.index">Content Flags</x-nav.sidebar-link>
            <x-nav.sidebar-link route="ldon-trap-templates.index" active="ldon-trap-templates.*">
                LDoN Trap Templates
            </x-nav.sidebar-link>
        </x-nav.sidebar-section>
        {{-- npcs --}}
        <x-nav.sidebar-section
            title="NPCs"
            section="npcs"
            :active="[
                'npcs.*',
                'merchants.*',
                'mounts.*',
                'pets.*',
                'beastlord-pets.*',
                'npc-emotes.*',
            ]"
        >
            <x-slot name="icon">
                <x-ui.icon name="npcs" />
            </x-slot>
            <x-nav.sidebar-link route="npcs.index" active="npcs.*">Npcs</x-nav.sidebar-link>
            <x-nav.sidebar-link route="merchants.index" active="merchants.*">Merchants</x-nav.sidebar-link>
            <x-nav.sidebar-link route="mounts.index">Mounts</x-nav.sidebar-link>
            <x-nav.sidebar-link :active="['pets.*', 'beastlord-pets.*']" label="Pets">
                <x-nav.sidebar-link route="pets.index">All Pets</x-nav.sidebar-link>
                <x-nav.sidebar-link route="pets.equipment.index">Equipment</x-nav.sidebar-link>
                <x-nav.sidebar-link route="beastlord-pets.index">Beastlord Pets</x-nav.sidebar-link>
            </x-nav.sidebar-link>
            <x-nav.sidebar-link route="npc-emotes.index">Emotes</x-nav.sidebar-link>
        </x-nav.sidebar-section>
        {{-- items --}}
        <x-nav.sidebar-section
            title="Items"
            section="items"
            :active="[
                'items.*',
                'tradeskills.*',
                'alt-currency.*',
                'books.*',
                'starting-items.*',
            ]"
        >
            <x-slot name="icon">
                <x-ui.icon name="items" />
            </x-slot>
            <x-nav.sidebar-link route="items.index" :active="['items.index', 'items.edit']">Items</x-nav.sidebar-link>
            <x-nav.sidebar-link route="tradeskills.index" active="tradeskills.*"
                :active="['tradeskills.index', 'tradeskills.edit']">Tradeskills</x-nav.sidebar-link>
            <x-nav.sidebar-link route="tradeskills.container-templates.index"
                active="tradeskills.container-templates.*">Tradeskill Container Templates</x-nav.sidebar-link>
            <x-nav.sidebar-link active="alt-currency.*" label="Alt Currency">
                <x-nav.sidebar-link route="alt-currency.index">Alt Currency</x-nav.sidebar-link>
                <x-nav.sidebar-link route="alt-currency.npcs.index">NPCs</x-nav.sidebar-link>
                <x-nav.sidebar-link route="alt-currency.characters.index">Characters</x-nav.sidebar-link>
            </x-nav.sidebar-link>
            <x-nav.sidebar-link route="items.evolving-items.index">Evolving Items</x-nav.sidebar-link>
            <x-nav.sidebar-link route="books.index">Books</x-nav.sidebar-link>
            <x-nav.sidebar-link route="starting-items.index">Starting Items</x-nav.sidebar-link>
        </x-nav.sidebar-section>
        {{-- spells and abilities  --}}
        <x-nav.sidebar-section
            title="Spells & Abilities"
            section="spells"
            :active="[
                'spells.*',
                'aa.*',
                'dbstr.*',
                'npc-spells.*',
                'npc-spell-effects.*',
                'auras.*',
                'tribute.*',
                'client-files.*',
            ]"
        >
            <x-slot name="icon">
                <x-ui.icon name="spells" />
            </x-slot>
            <x-nav.sidebar-link route="spells.index" active="spells.*">Spells</x-nav.sidebar-link>
            <x-nav.sidebar-link route="aa.index" active="aa.*">AAs</x-nav.sidebar-link>
            <x-nav.sidebar-link route="dbstr.index">DBStr</x-nav.sidebar-link>
            <x-nav.sidebar-link route="npc-spells.index" active="npc-spells.*">NPC Spells</x-nav.sidebar-link>
            <x-nav.sidebar-link route="npc-spell-effects.index">NPC Spell Effects</x-nav.sidebar-link>
            <x-nav.sidebar-link route="auras.index">Auras</x-nav.sidebar-link>
            <x-nav.sidebar-link route="tribute.index">Tribute</x-nav.sidebar-link>
            <x-nav.sidebar-link route="client-files.index">Client Files</x-nav.sidebar-link>
        </x-nav.sidebar-section>
        {{-- characters --}}
        <x-nav.sidebar-section
            title="Characters"
            section="characters"
            :active="[
                'characters.*',
                'guilds.*',
                'parcels.*',
                'titles.*',
            ]"
        >
            <x-slot name="icon">
                <x-ui.icon name="characters" />
            </x-slot>
            <x-nav.sidebar-link route="characters.index" :active="['characters.index', 'characters.show']">
                Characters
            </x-nav.sidebar-link>
            <x-nav.sidebar-link route="guilds.index" active="guilds.*">Guilds</x-nav.sidebar-link>
            <x-nav.sidebar-link route="parcels.index">Parcels</x-nav.sidebar-link>
            <x-nav.sidebar-link route="titles.index">Titles</x-nav.sidebar-link>
            <x-nav.sidebar-link route="characters.recipes">Recipes Made</x-nav.sidebar-link>
            <x-nav.sidebar-link route="characters.base-data.index" active="characters.base-data.*">
                Base Data
            </x-nav.sidebar-link>
        </x-nav.sidebar-section>
        {{-- loot --}}
        <x-nav.sidebar-section
            title="Loot"
            section="Loot"
            :active="[
                'loot.*',
                'global-loot.*',
            ]"
        >
            <x-slot name="icon">
                <x-ui.icon name="loot" />
            </x-slot>
            <x-nav.sidebar-link route="loot.index" :active="['loot.index','loot.edit']">
                Loot Tables
            </x-nav.sidebar-link>
            <x-nav.sidebar-link route="loot.drops.index" :active="['loot.drops.index','loot.drops.edit']">
                Loot Drops
            </x-nav.sidebar-link>
            <x-nav.sidebar-link route="global-loot.index" active="global-loot.*">Global</x-nav.sidebar-link>
        </x-nav.sidebar-section>
        {{-- factions --}}
        <x-nav.sidebar-section
            title="Faction"
            section="Faction"
            :active="[
                'factions.*',
            ]"
        >
            <x-slot name="icon">
                <x-ui.icon name="factions" />
            </x-slot>
            <x-nav.sidebar-link route="factions.edit" active="factions.edit">Faction List</x-nav.sidebar-link>
            <x-nav.sidebar-link route="factions.associations.index">Associations</x-nav.sidebar-link>
            <x-nav.sidebar-link route="factions.characters.index">Characters</x-nav.sidebar-link>
        </x-nav.sidebar-section>
        {{-- dynamic zones --}}
        <x-nav.sidebar-section
            title="Dynamic Zones"
            section="dzs"
            :active="[
                'dynamiczones.*',
            ]"
        >
            <x-slot name="icon">
                <x-ui.icon name="dzs" />
            </x-slot>
            <x-nav.sidebar-link route="dynamiczones.index">Dynamic Zones</x-nav.sidebar-link>
            <x-nav.sidebar-link route="dynamiczones.lockouts.index">DZ Lockouts</x-nav.sidebar-link>
            <x-nav.sidebar-link route="dynamiczones.character-lockouts.index">
                Character DZ Lockouts
            </x-nav.sidebar-link>
            <x-nav.sidebar-link route="dynamiczones.templates.index">DZ Templates</x-nav.sidebar-link>
        </x-nav.sidebar-section>
        {{-- logs --}}
        <x-nav.sidebar-section
            title="Logs"
            section="logs"
            :active="[
                'player-logs.*',
                'trader-audit.*',
                'player-logs.settings.*',
                'discord-webhooks.*',
            ]"
        >
            <x-slot name="icon">
                <x-ui.icon name="logs" />
            </x-slot>
            <x-nav.sidebar-link route="player-logs.index">Player Logs</x-nav.sidebar-link>
            <x-nav.sidebar-link route="trader-audit.index">Trader Audit</x-nav.sidebar-link>
            <x-nav.sidebar-link route="player-logs.settings.index" active="player-logs.settings.*">
                Player Log Settings
            </x-nav.sidebar-link>
            <x-nav.sidebar-link route="discord-webhooks.index">Discord Webhooks</x-nav.sidebar-link>
        </x-nav.sidebar-section>
        {{-- admin (visible to admin role only) --}}
        @role('admin')
            <x-nav.sidebar-section
                title="Admin"
                section="admin"
                :active="[
                    'accounts.*',
                    'chats.*',
                    'mail.*',
                    'server-rules.*',
                    'variables.*',
                ]"
            >
                <x-slot name="icon">
                    <x-ui.icon name="admin" />
                </x-slot>
                <x-nav.sidebar-link route="accounts.index" active="accounts.*">Accounts</x-nav.sidebar-link>
                <x-nav.sidebar-link route="chats.index">Chat Channels</x-nav.sidebar-link>
                <x-nav.sidebar-link route="mail.index">Mail</x-nav.sidebar-link>
                <x-nav.sidebar-link route="server-rules.index">Server Rules</x-nav.sidebar-link>
                <x-nav.sidebar-link route="variables.index">Variables</x-nav.sidebar-link>
            </x-nav.sidebar-section>
        @endrole
    </nav>
</aside>
