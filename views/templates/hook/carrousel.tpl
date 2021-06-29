{*
 * 2007-2020 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="m-b-1 m-t-1">
<div class="panel"><h2><i class="icon-list-ul"></i> {l s='Slides list' d='Modules.kj_productcarrousel.Admin'}
        <span class="panel-heading-action">
		<a id="desc-product-new" class="list-toolbar-btn" href="{$link->getAdminLink('ProductCarrouselControllerAdd',true,array(),["productId"=>"{$productId}"])}">
			<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Add new' d='Admin.Actions'}" data-html="true">
				<i class="material-icons">add</i>
			</span>
		</a>
	</span>
    </h2>
    <div id="carrouselContent">
        <div id="carrousels">
            {if !empty($carrousels)}
                {foreach from=$carrousels item=carrousel}
                    <div id="carrouses_{$carrousel.id}" class="panel">
                        <div class="row">
                            <div class="col-lg-1">
                                <span><i class="icon-arrows "></i></span>
                            </div>
                            <div class="col-md-3">
                                {assign var="name" value="."|explode:$carrousel.fileName}
                                {if $name[1]|in_array:$img}
                                    <img src="{$image_baseurl}{$carrousel.fileName}" alt="{$carrousel.nom}" class="img-thumbnail" />
                                {elseif $name[1]|in_array:$video}
                                    <video controls width="150">
                                        <source src="{$image_baseurl}{$carrousel.fileName}" type="video/mp4">
                                    </video>
                                {/if}

                            </div>
                            <div class="col-md-8">
                                <h4 class="pull-left">
                                    {$carrouse.fileName}
                                </h4>
                                <div class="btn-group-action pull-right">
                                    {$carrousel.position}
                                    <a class="btn btn-default"
                                       href="{$link->getAdminLink('ProductCarrouselController',true,[],['action' => 'edit',"productId"=>"{$productId}","carrouselId"=>"{$carrousel.id}"])}">
                                        <i class="icon-edit"></i>
                                        {l s='Edit' d='Admin.Actions'}
                                    </a>
                                    <a class="btn btn-default"
                                       href="{$link->getAdminLink('ProductCarrouselController',true,[],['action' => 'delete',"productId"=>"{$productId}","carrouselId"=>"{$carrousel.id}"])}">
                                        <i class="icon-trash"></i>
                                        {l s='Delete' d='Admin.Actions'}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                {/foreach}
            {/if}
        </div>
    </div>
</div>
</div>
