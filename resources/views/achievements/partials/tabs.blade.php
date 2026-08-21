<div class="tabs tabs-lift">
    <label class="tab" title="Name, presentation, score, enable state, and durable definition version">
        <input type="radio" name="achievement_tabs" class="tab" aria-label="General" checked="checked" />
        General
        <div class="badge badge-xs badge-soft badge-info ml-2" x-text="editor.associations.length"></div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('achievements.tabs.general')
    </div>

    <label class="tab" title="Choose where this definition appears in the client category tree">
        <input type="radio" name="achievement_tabs" class="tab" aria-label="Categories" />
        Categories
        <div class="badge badge-xs badge-soft badge-info ml-2" x-text="editor.associations.length"></div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('achievements.tabs.associations')
    </div>

    <label class="tab" title="Author client rows and the server events that advance them">
        <input type="radio" name="achievement_tabs" class="tab" aria-label="Components" />
        Components
        <div class="badge badge-xs badge-soft badge-info ml-2" x-text="editor.components.length"></div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('achievements.tabs.components')
    </div>

    <label class="tab" title="Configure automatic grants and selectable reward graphs">
        <input type="radio" name="achievement_tabs" class="tab" aria-label="Rewards" />
        Rewards
        <div class="badge badge-xs badge-soft badge-info ml-2" x-text="editor.rewards.length"></div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('achievements.tabs.rewards')
    </div>

    <label class="tab" title="Connect achievement completion to spell cast restrictions">
        <input type="radio" name="achievement_tabs" class="tab" aria-label="Cast Requirements" />
        Cast Requirements
        <div class="badge badge-xs badge-soft badge-info ml-2" x-text="editor.restrictions.length"></div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('achievements.tabs.restrictions')
    </div>

    <label class="tab" title="Review live authoring checks before saving and reloading the server catalog">
        <input type="radio" name="achievement_tabs" class="tab" aria-label="Validation" />
        Validation
        <div class="badge badge-xs badge-soft badge-info ml-2" x-show="validationIssues().length" x-text="validationIssues().length"></div>
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('achievements.tabs.validation')
    </div>

    <input type="radio" name="achievement_tabs" class="tab" aria-label="Authoring Guide"
        title="Open the built-in event, behavior, reward, and publishing reference" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('achievements.tabs.guide')
    </div>
</div>
