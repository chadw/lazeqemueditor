<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    <div class="card bg-base-100 shadow xl:col-span-2">
        <div class="card-body">
            <h2 class="card-title">Authoring Checks</h2>
            <p class="text-sm opacity-65">These immediate checks are repeated and expanded by server-side validation when you save.</p>
            <div class="space-y-2 mt-3">
                <template x-for="issue in validationIssues()" :key="issue.level + issue.message">
                    <div class="alert alert-soft" :class="issueClass(issue.level)">
                        <x-ui.icon name="warning" /> <span x-text="issue.message"></span>
                    </div>
                </template>
                <div x-show="validationIssues().length === 0" class="alert alert-soft alert-success">
                    <x-ui.icon name="save" /> No definition-local problems detected.
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Graph Summary</h2>
            <dl class="grid grid-cols-2 gap-3 text-sm mt-2">
                <dt class="opacity-60">Categories</dt><dd class="text-right tabular-nums" x-text="editor.associations.length"></dd>
                <dt class="opacity-60">Components</dt><dd class="text-right tabular-nums" x-text="editor.components.length"></dd>
                <dt class="opacity-60">Criteria</dt><dd class="text-right tabular-nums" x-text="editor.components.reduce((sum, row) => sum + row.criteria.length, 0)"></dd>
                <dt class="opacity-60">Grant rows</dt><dd class="text-right tabular-nums" x-text="editor.rewards.length"></dd>
                <dt class="opacity-60">Reward choices</dt><dd class="text-right tabular-nums" x-text="editor.reward_set.present ? editor.reward_set.options.length : 0"></dd>
                <dt class="opacity-60">Restrictions</dt><dd class="text-right tabular-nums" x-text="editor.restrictions.length"></dd>
            </dl>
            <div class="divider"></div>
            <p class="text-xs opacity-65">
                The authoritative zone reload validates the complete catalog, including category ancestors,
                dependency cycles, class applicability, and relationships to other definitions.
            </p>
        </div>
    </div>
</div>
