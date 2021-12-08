/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
import $ from"jquery";import{AbstractInteractableModule}from"TYPO3/CMS/Install/Module/AbstractInteractableModule.js";import Modal from"TYPO3/CMS/Backend/Modal.js";import Notification from"TYPO3/CMS/Backend/Notification.js";import AjaxRequest from"TYPO3/CMS/Core/Ajax/AjaxRequest.js";import Router from"TYPO3/CMS/Install/Router.js";class ClearTypo3tempFiles extends AbstractInteractableModule{constructor(){super(...arguments),this.selectorDeleteTrigger=".t3js-clearTypo3temp-delete",this.selectorOutputContainer=".t3js-clearTypo3temp-output",this.selectorStatContainer=".t3js-clearTypo3temp-stat-container",this.selectorStatsTrigger=".t3js-clearTypo3temp-stats",this.selectorStatTemplate=".t3js-clearTypo3temp-stat-template",this.selectorStatNumberOfFiles=".t3js-clearTypo3temp-stat-numberOfFiles",this.selectorStatDirectory=".t3js-clearTypo3temp-stat-directory"}initialize(t){this.currentModal=t,this.getStats(),t.on("click",this.selectorStatsTrigger,t=>{t.preventDefault(),$(this.selectorOutputContainer).empty(),this.getStats()}),t.on("click",this.selectorDeleteTrigger,t=>{const e=$(t.currentTarget).data("folder"),s=$(t.currentTarget).data("storage-uid");t.preventDefault(),this.delete(e,s)})}getStats(){this.setModalButtonsState(!1);const t=this.getModalBody();new AjaxRequest(Router.getUrl("clearTypo3tempFilesStats")).get({cache:"no-cache"}).then(async e=>{const s=await e.resolve();!0===s.success?(t.empty().append(s.html),Modal.setButtons(s.buttons),Array.isArray(s.stats)&&s.stats.length>0&&s.stats.forEach(e=>{if(e.numberOfFiles>0){const s=t.find(this.selectorStatTemplate).clone();s.find(this.selectorStatNumberOfFiles).text(e.numberOfFiles),s.find(this.selectorStatDirectory).text(e.directory),s.find(this.selectorDeleteTrigger).attr("data-folder",e.directory),s.find(this.selectorDeleteTrigger).attr("data-storage-uid",e.storageUid),t.find(this.selectorStatContainer).append(s.html())}})):Notification.error("Something went wrong","The request was not processed successfully. Please check the browser's console and TYPO3's log.")},e=>{Router.handleAjaxError(e,t)})}delete(t,e){const s=this.getModalBody(),r=this.getModuleContent().data("clear-typo3temp-delete-token");new AjaxRequest(Router.getUrl()).post({install:{action:"clearTypo3tempFiles",token:r,folder:t,storageUid:e}}).then(async t=>{const e=await t.resolve();!0===e.success&&Array.isArray(e.status)?(e.status.forEach(t=>{Notification.success(t.title,t.message)}),this.getStats()):Notification.error("Something went wrong","The request was not processed successfully. Please check the browser's console and TYPO3's log.")},t=>{Router.handleAjaxError(t,s)})}}export default new ClearTypo3tempFiles;