<template>
	<div class="pg-settings">

		<!--/* Category Color Option */-->
		<div class="pg-field" v-if="fields.category_color_option">
			<div class="pg-field__info">
				<label class="pg-field__label">{{ fields.category_color_option.label }}</label>
				<p class="pg-field__desc" v-html="fields.category_color_option.description"></p>
			</div>
			<div class="pg-field__control">
				<BaseToggle
					v-model="pg_options.show_category_color"
					:on-label="l10n.show"
					:off-label="l10n.hide"
					:aria-label="fields.category_color_option.label"
				/>
			</div>
		</div>

		<!--/* Category Ordering */-->
		<div class="pg-field">
			<div class="pg-field__info">
				<label for="anwp-pg-input-category-ordering" class="pg-field__label">{{ fields.category_ordering.label }}</label>
				<p class="pg-field__desc" v-html="fields.category_ordering.description"></p>
			</div>
			<div class="pg-field__control">
				<select id="anwp-pg-input-category-ordering" class="pg-select" v-model="pg_options.category_ordering">
					<option value="">- default -</option>
					<option value="parent_children">Parent > Children</option>
					<option value="children_parent">Children > Parent</option>
				</select>
			</div>
		</div>

		<!--/* Default featured image */-->
		<div class="pg-field" v-if="fields.default_featured_image">
			<div class="pg-field__info">
				<label class="pg-field__label">{{ fields.default_featured_image.label }}</label>
				<p class="pg-field__desc" v-html="fields.default_featured_image.description"></p>
			</div>
			<div class="pg-field__control">
				<div class="pg-image">
					<div class="pg-image__preview" v-if="pg_options.default_featured_image">
						<img :src="pg_options.default_featured_image__url" alt="">
						<a href="#" @click.prevent="pg_options.default_featured_image = ''" class="pg-image__remove">{{ l10n.remove_image }}</a>
					</div>
					<button type="button" class="button button-secondary" @click="openMedia('default_featured_image')">
						{{ l10n.select_image }}
					</button>
				</div>
			</div>
		</div>

		<!--/* Image Rendering */-->
		<div class="pg-field" v-if="fields.image_rendering">
			<div class="pg-field__info">
				<label class="pg-field__label">{{ fields.image_rendering.label }}</label>
				<p class="pg-field__desc" v-html="fields.image_rendering.description"></p>
			</div>
			<div class="pg-field__control">
				<select id="anwp-pg-input-image-rendering" class="pg-select" v-model="pg_options.image_rendering">
					<option value="img">"img" tag</option>
					<option value="div_bg">div tag with image background</option>
				</select>
			</div>
		</div>

		<!--/* CPT */-->
		<div class="pg-field" v-if="fields.cpt_support">
			<div class="pg-field__info">
				<label class="pg-field__label">{{ fields.cpt_support.label }}</label>
				<p class="pg-field__desc" v-html="fields.cpt_support.description"></p>
			</div>
			<div class="pg-field__control">
				<ul class="pg-list" v-if="pg_options.cpt_support && pg_options.cpt_support.length">
					<li class="pg-list__item" v-for="(cpt, cpt_index) in pg_options.cpt_support" :key="cpt_index">
						<span class="pg-list__title">{{ cpt.label }} <span class="pg-list__muted">({{ cpt.name }})</span></span>
						<span class="pg-list__meta">{{ cpt.image_meta }} ({{ cpt.image_type }})</span>
						<span class="pg-list__actions">
							<button class="pg-icon-btn" type="button" title="Edit" @click.prevent="editSupportedCPT( cpt_index )">
								<svg class="pg-ico"><use xlink:href="#icon-anwp-pg-pencil"></use></svg>
							</button>
							<button class="pg-icon-btn pg-icon-btn--danger" type="button" title="Delete" @click.prevent="modalCPTDeleteConfirmOpen( cpt_index )">
								<svg class="pg-ico"><use xlink:href="#icon-anwp-pg-trash"></use></svg>
							</button>
						</span>
					</li>
				</ul>
				<p class="pg-empty" v-else>{{ '— no CPTs added —' }}</p>

				<button type="button" class="button button-secondary pg-add-btn" @click.prevent="editSupportedCPT()">
					<span class="pg-add-btn__plus">+</span> Add CPT
				</button>
			</div>
		</div>

		<!--/* Post Icons */-->
		<div class="pg-field" v-if="fields.post_icons">
			<div class="pg-field__info">
				<label class="pg-field__label">{{ fields.post_icons.label }}</label>
				<p class="pg-field__desc" v-html="fields.post_icons.description"></p>
			</div>
			<div class="pg-field__control">
				<ul class="pg-list" v-if="pg_options.post_icons && pg_options.post_icons.length">
					<li class="pg-list__item" v-for="(post_icon, post_icon_index) in pg_options.post_icons" :key="post_icon_index">
						<span class="pg-list__icon">
							<img v-if="post_icon.icon && mapSlugToIconUrl[post_icon.icon]" :src="mapSlugToIconUrl[post_icon.icon]" alt="post icon">
							<img v-else-if="post_icon.custom_icon" :src="post_icon.custom_icon" alt="post icon">
						</span>
						<span class="pg-list__title pg-list__title--cap">{{ formatIconType( post_icon.type ) }}</span>
						<span class="pg-list__meta pg-list__meta--cap">{{ type_options.map[post_icon.type_id] }}</span>
						<span class="pg-list__actions">
							<button class="pg-icon-btn" type="button" title="Edit" @click.prevent="editPostIconRule( post_icon_index )">
								<svg class="pg-ico"><use xlink:href="#icon-anwp-pg-pencil"></use></svg>
							</button>
							<button class="pg-icon-btn pg-icon-btn--danger" type="button" title="Delete" @click.prevent="modalPostIconDeleteConfirmOpen( post_icon_index )">
								<svg class="pg-ico"><use xlink:href="#icon-anwp-pg-trash"></use></svg>
							</button>
						</span>
					</li>
				</ul>
				<p class="pg-empty" v-else>{{ '— no rules created —' }}</p>

				<button type="button" class="button button-secondary pg-add-btn" @click.prevent="editPostIconRule()">
					<span class="pg-add-btn__plus">+</span> Add New Rule
				</button>
			</div>
		</div>

		<!--/* Open Links in New Tab */-->
		<div class="pg-field" v-if="fields.link_open_new_tab">
			<div class="pg-field__info">
				<label class="pg-field__label">{{ fields.link_open_new_tab.label }}</label>
				<p class="pg-field__desc" v-html="fields.link_open_new_tab.description"></p>
			</div>
			<div class="pg-field__control">
				<BaseToggle
					v-model="pg_options.link_open_new_tab"
					:on-label="l10n.yes"
					:off-label="l10n.no"
					:aria-label="fields.link_open_new_tab.label"
				/>
			</div>
		</div>

		<!--/* Save */-->
		<div class="pg-actions">
			<button class="button button-primary button-large" type="button" @click.prevent="saveOptions" :disabled="activeRequest">
				{{ l10n.save_options }}
			</button>
			<span class="spinner pg-spinner" :class="{ 'is-active': activeRequest }"></span>
		</div>

		<!--/* Modal - Post Icon */-->
		<BaseModal ref="modalPostIconRef">
			<template #header>
				<h3 class="pg-modal__title">{{ modalPostIconSelectMode ? 'Select Icon' : 'Add / Edit Rule' }}</h3>
			</template>

			<template v-if="modalPostIconSelectMode">
				<div class="pg-icongrid">
					<button type="button" class="pg-icongrid__btn"
							v-for="octiIcon in octiIcons" :key="octiIcon.slug"
							:title="octiIcon.slug"
							@click.prevent="setPostIcon( octiIcon.slug )">
						<img :src="octiIcon.url" alt="icon">
					</button>
				</div>
			</template>

			<template v-else>
				<div class="pg-mfield">
					<label class="pg-mlabel" for="anwp-pg-input-rule-type">Rule Type</label>
					<select id="anwp-pg-input-rule-type" class="pg-select pg-select--full" v-model="modalPostIconData.type" @change="modalPostIconData.type_id = ''">
						<option value="">- select -</option>
						<option value="category">Category</option>
						<option value="tag">Tag</option>
						<option value="post_format">Post Format</option>
					</select>
				</div>

				<div class="pg-mfield">
					<label class="pg-mlabel" for="anwp-pg-input-rule-type-value">Rule Type Value</label>
					<select class="pg-select pg-select--full" v-model="modalPostIconData.type_id" id="anwp-pg-input-rule-type-value">
						<option value="">- select -</option>
						<option v-for="type_option in modalTypeOptions" :value="type_option.id" :key="type_option.id">{{ type_option.title }}</option>
					</select>
				</div>

				<div class="pg-mfield">
					<div class="pg-mlabel pg-mlabel--row">
						<span>Post Icon</span>
						<a href="#" class="pg-mlabel__remove"
								v-if="modalPostIconData.icon || modalPostIconData.custom_icon"
								@click.prevent="deletePostIcon">remove icon</a>
					</div>

					<div class="pg-iconpick">
						<button type="button" class="button button-secondary" @click="modalPostIconSelectMode = true">Select Icon</button>
						<button type="button" class="button button-secondary" @click="openMedia( 'post_icons' )">Load Custom Icon</button>

						<span class="pg-iconpick__preview" v-if="modalPostIconData.icon && mapSlugToIconUrl[modalPostIconData.icon]">
							<img :src="mapSlugToIconUrl[modalPostIconData.icon]" alt="post icon">
						</span>
						<span class="pg-iconpick__preview" v-else-if="modalPostIconData.custom_icon">
							<img :src="modalPostIconData.custom_icon" alt="post icon">
						</span>
					</div>

					<p class="pg-hint">
						<b>Select Icon</b> - select from a list of predefined icons<br>
						<b>Load Custom Icon</b> - load a custom image via the WordPress Media Library. White images recommended.
					</p>
				</div>
			</template>

			<template #footer>
				<template v-if="modalPostIconSelectMode">
					<button type="button" class="button button-secondary" @click.prevent="modalPostIconSelectMode = false">Cancel</button>
				</template>
				<template v-else>
					<button @click.prevent="addPostIconRule" type="button" class="button button-primary">Apply Changes</button>
					<button type="button" class="button button-secondary" @click.prevent="closePostIconModal">Close</button>
				</template>
			</template>
		</BaseModal>

		<!--/* Modal - CPT */-->
		<BaseModal ref="modalCPTRef">
			<template #header>
				<h3 class="pg-modal__title">Add / Edit CPT</h3>
			</template>

			<div class="pg-mfield">
				<label class="pg-mlabel" for="anwp-pg-input-cpt">CPT Name</label>
				<select id="anwp-pg-input-cpt" class="pg-select pg-select--full" v-model="modalCPTData.name" @change="cptSetDefaultImage()">
					<option value="">- select -</option>
					<option :value="cpt_option.name" v-for="cpt_option in cpt_options" :key="cpt_option.name">{{ cpt_option.label }}</option>
				</select>
			</div>

			<div class="pg-mfield">
				<label class="pg-mlabel" for="anwp-pg-input-img-meta-value">Image Meta Field</label>
				<select class="pg-select pg-select--full" v-model="modalCPTData.image_meta" id="anwp-pg-input-img-meta-value">
					<option value="">- select -</option>
					<option v-for="img_meta in cptImageFields" :value="img_meta" :key="img_meta">{{ img_meta }}</option>
				</select>
				<p class="pg-hint">
					Different CPTs store images in different ways. Select the appropriate image metadata field and image type.
				</p>
			</div>

			<div class="pg-mfield">
				<label class="pg-mlabel" for="anwp-pg-input-cpt-image-type-value">Image Type</label>
				<select class="pg-select pg-select--full" v-model="modalCPTData.image_type" id="anwp-pg-input-cpt-image-type-value">
					<option value="id">image id</option>
					<option value="url">image url</option>
				</select>
			</div>

			<template #footer>
				<button @click.prevent="addCPTSupport" type="button" class="button button-primary">Apply Changes</button>
				<button type="button" class="button button-secondary" @click.prevent="closeCPTModal">Close</button>
			</template>
		</BaseModal>

		<!--/* Modal - Delete Rule */-->
		<BaseModal ref="modalPostIconDeleteRef">
			<template #header>
				<h4 class="pg-modal__title">Confirm Delete</h4>
			</template>

			<div class="pg-confirm">
				<span class="pg-confirm__icon"><svg class="pg-ico"><use xlink:href="#icon-anwp-pg-trash"></use></svg></span>
				<p class="pg-confirm__lead">Are you sure?</p>
				<p class="pg-confirm__text">Do you really want to delete this Icon Rule?</p>
			</div>

			<template #footer>
				<button type="button" class="button button-secondary" @click.prevent="modalPostIconDeleteConfirmClose">Close</button>
				<button @click.prevent="modalPostIconDelete" type="button" class="button pg-btn-delete">Delete Icon Rule</button>
			</template>
		</BaseModal>

		<!--/* Modal - Delete CPT */-->
		<BaseModal ref="modalCPTDeleteRef">
			<template #header>
				<h4 class="pg-modal__title">Confirm Delete</h4>
			</template>

			<div class="pg-confirm">
				<span class="pg-confirm__icon"><svg class="pg-ico"><use xlink:href="#icon-anwp-pg-trash"></use></svg></span>
				<p class="pg-confirm__lead">Are you sure?</p>
				<p class="pg-confirm__text">Do you really want to delete this CPT?</p>
			</div>

			<template #footer>
				<button type="button" class="button button-secondary" @click.prevent="modalCPTDeleteConfirmClose">Close</button>
				<button @click.prevent="modalCPTDelete" type="button" class="button pg-btn-delete">Delete CPT</button>
			</template>
		</BaseModal>

		<ToastHost />
	</div>
</template>

<script setup>
	import { reactive, ref, computed } from 'vue';

	import BaseModal from './components/BaseModal.vue';
	import BaseToggle from './components/BaseToggle.vue';
	import ToastHost from './components/ToastHost.vue';
	import { useToast } from './composables/useToast';

	const { notify } = useToast();

	/*
	|--------------------------------------------------------------------
	| Read-only config injected via inline <script> in admin/views/settings.php
	|--------------------------------------------------------------------
	*/
	const fields       = window._anwp_pg_settings_fields;
	const type_options = window._anwp_pg_settings_types;
	const octiIcons    = window._anwp_pg_octi_icons;
	const cpt_options  = window._anwp_pg_cpt_options;
	const l10n         = window._anwp_pg_settings_text;

	/*
	|--------------------------------------------------------------------
	| Mutable state
	|--------------------------------------------------------------------
	*/
	const pg_options = reactive( structuredClone( window._anwp_pg_settings_data ) );

	// Convert stored 'yes'/'no' strings to booleans for the toggles.
	pg_options.show_category_color = ( 'yes' === pg_options.show_category_color );
	pg_options.link_open_new_tab   = ( 'yes' === pg_options.link_open_new_tab );

	const activeRequest = ref( false );

	// Post Icon modal state
	const modalPostIconData        = ref( {} );
	const modalPostIconSelectMode  = ref( false );
	const modalPostIconRemoveIndex = ref( false );

	// CPT modal state
	const modalCPTData = ref( {
		name: '',
		label: '',
		image_meta: '',
		image_type: 'id',
	} );
	const modalCPTRemoveIndex = ref( false );

	// Template refs to modals
	const modalPostIconRef       = ref( null );
	const modalCPTRef            = ref( null );
	const modalPostIconDeleteRef = ref( null );
	const modalCPTDeleteRef      = ref( null );

	/*
	|--------------------------------------------------------------------
	| WordPress Media uploader
	|--------------------------------------------------------------------
	*/
	const mediaActiveField   = ref( '' );
	const mediaImageUploader = wp.media( {
		title: l10n.select_image,
		library: {
			type: 'image',
		},
		button: {
			text: l10n.select_image,
		},
		multiple: false,
	} );

	/*
	|--------------------------------------------------------------------
	| Computed
	|--------------------------------------------------------------------
	*/
	const mapSlugToIconUrl = computed( () => {
		const output = {};

		for ( const icon of octiIcons ) {
			output[ icon.slug ] = icon.url;
		}

		return output;
	} );

	const modalTypeOptions = computed( () => {
		const type = modalPostIconData.value.type;

		if ( type_options && type && type_options[ type ] ) {
			return type_options[ type ];
		}

		return {};
	} );

	const cptImageFields = computed( () => {
		const found = cpt_options.find( ( o ) => o.name === modalCPTData.value.name );

		return found ? found.meta : [];
	} );

	/*
	|--------------------------------------------------------------------
	| Helpers
	|--------------------------------------------------------------------
	*/
	function formatIconType( value ) {
		return value ? value.replaceAll( '_', ' ' ) : '';
	}

	/*
	|--------------------------------------------------------------------
	| Save
	|--------------------------------------------------------------------
	*/
	async function saveOptions() {

		if ( activeRequest.value ) {
			return;
		}

		activeRequest.value = true;

		try {
			const response = await fetch( l10n.restUrl + 'anwp-pg/v1/settings', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': l10n.restNonce,
				},
				body: JSON.stringify( { pg_options } ),
			} );

			const data = response.ok ? await response.json() : null;

			if ( data && true === data.updated ) {
				notify( 'success', l10n.saved_successfully );
			} else if ( data && false === data.updated ) {
				notify( 'warning', l10n.nothing_to_update );
			} else {
				notify( 'error', l10n.data_save_error );
			}
		} catch ( e ) {
			notify( 'error', l10n.data_save_error );
		} finally {
			activeRequest.value = false;
		}
	}

	/*
	|--------------------------------------------------------------------
	| Media
	|--------------------------------------------------------------------
	*/
	function openMedia( activeField ) {

		mediaActiveField.value = activeField;

		const fromModal = ( 'post_icons' === activeField );

		// Reset prior one-shot handlers so they neither stack nor fire in the wrong context.
		mediaImageUploader.off( 'select' );
		mediaImageUploader.off( 'close' );

		mediaImageUploader.on( 'select', () => {
			setMediaUploaderFile( mediaImageUploader.state().get( 'selection' ).first().toJSON() );
		} );

		if ( fromModal ) {
			// The rule modal is a native <dialog> opened with showModal(), so it lives in the
			// browser top layer — above every z-index, including the wp.media modal. Step it out
			// of the top layer while the media frame is open, then restore it when that closes
			// (the picked icon is in reactive state, so it shows on return).
			modalPostIconRef.value.close();
			mediaImageUploader.on( 'close', () => {
				modalPostIconRef.value.open();
			} );
		}

		mediaImageUploader.open();
	}

	function setMediaUploaderFile( attachment ) {

		if ( 'undefined' !== typeof attachment && attachment.url && mediaActiveField.value ) {

			switch ( mediaActiveField.value ) {
				case 'default_featured_image':
					pg_options.default_featured_image__url = attachment.url;
					pg_options.default_featured_image      = attachment.id;
					break;

				case 'post_icons':
					modalPostIconData.value.icon           = '';
					modalPostIconData.value.custom_icon    = attachment.url;
					modalPostIconData.value.custom_icon_id = attachment.id;
					break;
			}
		}
	}

	/*
	|--------------------------------------------------------------------
	| Post Icon rules
	|--------------------------------------------------------------------
	*/
	function editPostIconRule( ruleIndex = null ) {

		if ( modalPostIconSelectMode.value ) {
			modalPostIconSelectMode.value = false;
		}

		if ( null !== ruleIndex ) {
			modalPostIconData.value = { ...pg_options.post_icons[ ruleIndex ], ruleIndex };
		} else {
			modalPostIconData.value = {
				icon: '',
				custom_icon: '',
				custom_icon_id: '',
				type: '',
				type_id: '',
				ruleIndex: null,
			};
		}

		modalPostIconRef.value.open();
	}

	function closePostIconModal() {
		modalPostIconRef.value.close();
	}

	function deletePostIcon() {
		modalPostIconData.value.icon           = '';
		modalPostIconData.value.custom_icon    = '';
		modalPostIconData.value.custom_icon_id = '';
	}

	function setPostIcon( slug ) {
		modalPostIconData.value.icon           = slug;
		modalPostIconData.value.custom_icon    = '';
		modalPostIconData.value.custom_icon_id = '';
		modalPostIconSelectMode.value          = false;
	}

	function addPostIconRule() {

		const errorData = [];

		if ( ! modalPostIconData.value.type ) {
			errorData.push( 'Rule Type' );
		}

		if ( ! modalPostIconData.value.type_id ) {
			errorData.push( 'Rule Type Value' );
		}

		if ( ! ( modalPostIconData.value.icon || modalPostIconData.value.custom_icon ) ) {
			errorData.push( 'Post Icon' );
		}

		if ( errorData.length ) {
			notify( 'error', 'Required Fields: <br> - ' + errorData.join( '<br> - ' ) );
			return;
		}

		const { ruleIndex, ...ruleToSave } = modalPostIconData.value;

		if ( null !== ruleIndex ) {
			pg_options.post_icons.splice( ruleIndex, 1, ruleToSave );
		} else {
			pg_options.post_icons.push( ruleToSave );
		}

		closePostIconModal();
	}

	function modalPostIconDeleteConfirmOpen( post_icon_index ) {
		modalPostIconRemoveIndex.value = post_icon_index;
		modalPostIconDeleteRef.value.open();
	}

	function modalPostIconDeleteConfirmClose() {
		modalPostIconDeleteRef.value.close();
	}

	function modalPostIconDelete() {
		// Try to delete if index is set
		if ( false !== modalPostIconRemoveIndex.value ) {
			pg_options.post_icons.splice( modalPostIconRemoveIndex.value, 1 );
		}

		modalPostIconRemoveIndex.value = false;
		modalPostIconDeleteConfirmClose();
	}

	/*
	|--------------------------------------------------------------------
	| CPT support
	|--------------------------------------------------------------------
	*/
	function editSupportedCPT( cptIndex = null ) {

		if ( null !== cptIndex ) {
			modalCPTData.value = {
				name: pg_options.cpt_support[ cptIndex ].name,
				label: '',
				image_meta: pg_options.cpt_support[ cptIndex ].image_meta,
				image_type: pg_options.cpt_support[ cptIndex ].image_type,
				cptIndex,
			};
		} else {
			modalCPTData.value = {
				name: '',
				label: '',
				image_meta: '',
				image_type: 'id',
				cptIndex: null,
			};
		}

		modalCPTRef.value.open();
	}

	function cptSetDefaultImage() {

		if ( ! modalCPTData.value.name ) {
			return;
		}

		const found = cpt_options.find( ( o ) => o.name === modalCPTData.value.name );

		modalCPTData.value.image_meta = found.image;
		modalCPTData.value.image_type = found.image_type;
	}

	function closeCPTModal() {
		modalCPTRef.value.close();
	}

	function addCPTSupport() {

		const errorData = [];

		if ( ! modalCPTData.value.name ) {
			errorData.push( 'CPT Name' );
		}

		if ( ! modalCPTData.value.image_meta ) {
			errorData.push( 'CPT Image' );
		}

		if ( ! modalCPTData.value.image_type ) {
			errorData.push( 'CPT Image Type' );
		}

		if ( errorData.length ) {
			notify( 'error', 'Required Fields: <br> - ' + errorData.join( '<br> - ' ) );
			return;
		}

		const found = cpt_options.find( ( o ) => o.name === modalCPTData.value.name );

		const CPTToSave = {
			name: modalCPTData.value.name,
			label: found.label,
			image_meta: modalCPTData.value.image_meta,
			image_type: modalCPTData.value.image_type,
		};

		if ( null !== modalCPTData.value.cptIndex ) {
			pg_options.cpt_support.splice( modalCPTData.value.cptIndex, 1, CPTToSave );
		} else {
			pg_options.cpt_support.push( CPTToSave );
		}

		closeCPTModal();
	}

	function modalCPTDeleteConfirmOpen( cpt_index ) {
		modalCPTRemoveIndex.value = cpt_index;
		modalCPTDeleteRef.value.open();
	}

	function modalCPTDeleteConfirmClose() {
		modalCPTDeleteRef.value.close();
	}

	function modalCPTDelete() {
		// Try to delete if index is set
		if ( false !== modalCPTRemoveIndex.value ) {
			pg_options.cpt_support.splice( modalCPTRemoveIndex.value, 1 );
		}

		modalCPTRemoveIndex.value = false;
		modalCPTDeleteConfirmClose();
	}
</script>

<style scoped>
	.pg-settings {
		max-width: 920px;
		color: #1d2327;
		font-size: 14px;
	}

	/* ---- Field rows: split-column (label left, control right) ---- */
	.pg-field {
		display: grid;
		grid-template-columns: minmax(0, 300px) minmax(0, 1fr);
		gap: 8px 40px;
		padding: 24px 0;
		border-top: 1px solid #e4e6e8;
		align-items: start;
	}

	.pg-field:first-child {
		border-top: 0;
		padding-top: 8px;
	}

	.pg-field__label {
		display: block;
		font-size: 14px;
		font-weight: 600;
		line-height: 1.4;
		color: #1d2327;
	}

	.pg-field__desc {
		margin: 6px 0 0;
		font-size: 13px;
		line-height: 1.5;
		color: #646970;
	}

	.pg-field__desc :deep(a) {
		color: #2271b1;
	}

	.pg-field__control {
		min-width: 0;
	}

	/* ---- Selects ---- */
	.pg-select {
		min-width: 260px;
		max-width: 100%;
		height: 34px;
		padding: 0 24px 0 10px;
	}

	.pg-select--full {
		width: 100%;
		min-width: 0;
	}

	/* ---- Default featured image ---- */
	.pg-image {
		display: flex;
		align-items: flex-start;
		gap: 16px;
	}

	.pg-image__preview {
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 4px;
	}

	.pg-image__preview img {
		width: 110px;
		height: 80px;
		object-fit: contain;
		padding: 4px;
		border: 1px solid #dcdcde;
		border-radius: 4px;
		background: #fff;
	}

	.pg-image__remove {
		font-size: 12px;
		color: #b32d2e;
		text-decoration: none;
	}

	.pg-image__remove:hover {
		text-decoration: underline;
	}

	/* ---- Lists (CPT + Post Icons) ---- */
	.pg-list {
		margin: 0 0 12px;
		padding: 0;
		list-style: none;
		border: 1px solid #e0e0e2;
		border-radius: 6px;
		overflow: hidden;
	}

	.pg-list__item {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 10px 12px;
		background: #fff;
		border-top: 1px solid #f0f0f1;
	}

	.pg-list__item:first-child {
		border-top: 0;
	}

	.pg-list__item:nth-child(even) {
		background: #fbfbfc;
	}

	.pg-list__icon {
		flex-shrink: 0;
		width: 32px;
		height: 32px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 4px;
		/* Octicons are white SVGs — dark chip so they stay visible. */
		background: #6c757d;
	}

	.pg-list__icon img {
		width: 22px;
		height: 22px;
		object-fit: contain;
	}

	.pg-list__title {
		font-weight: 600;
		color: #1d2327;
	}

	.pg-list__title--cap {
		text-transform: capitalize;
	}

	.pg-list__muted {
		font-weight: 400;
		color: #787c82;
	}

	.pg-list__meta {
		color: #646970;
		font-size: 13px;
	}

	.pg-list__meta--cap {
		text-transform: capitalize;
	}

	.pg-list__actions {
		margin-left: auto;
		display: flex;
		gap: 6px;
		flex-shrink: 0;
	}

	.pg-empty {
		margin: 0 0 12px;
		color: #787c82;
		font-style: italic;
	}

	/* ---- Icon buttons ---- */
	.pg-icon-btn {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 32px;
		height: 32px;
		padding: 0;
		background: #fff;
		border: 1px solid #c3c4c7;
		border-radius: 4px;
		color: #50575e;
		cursor: pointer;
		transition: border-color 0.15s ease, color 0.15s ease, background-color 0.15s ease;
	}

	.pg-icon-btn:hover {
		border-color: #2271b1;
		color: #2271b1;
	}

	.pg-icon-btn--danger:hover {
		border-color: #b32d2e;
		color: #b32d2e;
		background: #fcf0f1;
	}

	.pg-ico {
		width: 16px;
		height: 16px;
		fill: currentColor;
	}

	/* ---- Add button ---- */
	.pg-add-btn {
		display: inline-flex;
		align-items: center;
		gap: 4px;
	}

	.pg-add-btn__plus {
		font-size: 16px;
		line-height: 1;
		font-weight: 600;
	}

	/* ---- Save action bar ---- */
	.pg-actions {
		display: flex;
		align-items: center;
		margin-top: 8px;
		padding-top: 24px;
		border-top: 1px solid #e4e6e8;
	}

	.pg-spinner {
		float: none;
		margin: 0 0 0 8px;
	}

	/* ---- Modals ---- */
	.pg-modal__title {
		margin: 0;
		font-size: 16px;
		font-weight: 600;
	}

	.pg-mfield {
		margin-bottom: 18px;
	}

	.pg-mfield:last-of-type {
		margin-bottom: 0;
	}

	.pg-mlabel {
		display: block;
		margin-bottom: 6px;
		font-size: 13px;
		font-weight: 600;
		color: #1d2327;
	}

	.pg-mlabel--row {
		display: flex;
		align-items: center;
	}

	.pg-mlabel__remove {
		margin-left: auto;
		font-size: 12px;
		font-weight: 400;
		color: #b32d2e;
		text-decoration: none;
	}

	.pg-mlabel__remove:hover {
		text-decoration: underline;
	}

	.pg-iconpick {
		display: flex;
		align-items: center;
		gap: 8px;
		margin: 8px 0;
	}

	.pg-iconpick__preview {
		margin-left: auto;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 36px;
		height: 36px;
		border-radius: 4px;
		background: #6c757d;
	}

	.pg-iconpick__preview img {
		width: 26px;
		height: 26px;
		object-fit: contain;
	}

	.pg-hint {
		margin: 8px 0 0;
		font-size: 12px;
		line-height: 1.5;
		color: #787c82;
	}

	/* ---- Icon picker grid ---- */
	.pg-icongrid {
		display: flex;
		flex-wrap: wrap;
		gap: 8px;
	}

	.pg-icongrid__btn {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 40px;
		height: 40px;
		padding: 4px;
		/* Octicons are white SVGs — dark cell so they stay visible. */
		background: #6c757d;
		border: 1px solid #5a6169;
		border-radius: 4px;
		cursor: pointer;
		transition: border-color 0.15s ease, background-color 0.15s ease;
	}

	.pg-icongrid__btn:hover {
		border-color: #2271b1;
		background: #7d868f;
	}

	.pg-icongrid__btn img {
		width: 24px;
		height: 24px;
		object-fit: contain;
	}

	/* ---- Delete confirm ---- */
	.pg-confirm {
		text-align: center;
		padding: 8px 0 4px;
	}

	.pg-confirm__icon {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 48px;
		height: 48px;
		margin-bottom: 8px;
		border-radius: 50%;
		background: #fcf0f1;
		color: #b32d2e;
	}

	.pg-confirm__icon .pg-ico {
		width: 22px;
		height: 22px;
	}

	.pg-confirm__lead {
		margin: 0 0 4px;
		font-size: 16px;
		font-weight: 600;
	}

	.pg-confirm__text {
		margin: 0;
		color: #646970;
	}

	.pg-btn-delete {
		color: #b32d2e;
		border-color: #b32d2e;
	}

	.pg-btn-delete:hover {
		background: #b32d2e;
		color: #fff;
		border-color: #b32d2e;
	}

	/* ---- Responsive: stack columns ---- */
	@media screen and (max-width: 782px) {
		.pg-field {
			grid-template-columns: 1fr;
			gap: 12px;
		}

		.pg-select {
			min-width: 0;
			width: 100%;
		}
	}
</style>
