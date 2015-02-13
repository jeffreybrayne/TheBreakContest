<div class="container" id="contestHeader">
	<div class="row">
		<div class="col12 last center">
			{if $quotaId == 1}
			{jrCore_image class="contest-logo" id="electricAdventureImage" skin="jrElastic" image="ElectricAdventureLogo.jpg" width="70%" alt="Electric Adventure"}
			<p>The Electric Adventure contest is...</p>
			{elseif $quotaId == 2}
			{jrCore_image class="contest-logo" id="runaroundImage" skin="jrElastic" image="RunaroundLogo.jpg" width="70%" alt="Runaround Tour"}
			<p>The Runaround Contest is...</p>
			{else}
			{jrCore_image class="contest-logo" id="surfSkateImage" skin="jrElastic" image="SkateAndSurfLogo.jpg" width="70%" alt="Surf and Skate Contest"}
			<p>The Skate and Surf contest is...</p>
			{/if}
		</div>
	</div>
</div>