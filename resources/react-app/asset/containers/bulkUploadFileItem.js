import React, {Component} from "react"
import {connect} from "react-redux"
import SelectTypeDropdown from "../components/selectTypeDropdown"
import InputText from "../components/inputText"
import InputPlaceholder from "../components/inputPlaceholder"
import DropdownTags from "../components/selectTagsDropdown"
import TagContainer  from "../components/tagContainer"
import TagRowContainer from "../components/tagRowContainer"
import Modal from "../components/modal";
import { runAction } from "../actions/assetActions"
import axios from 'axios';

class FileTable extends Component {

    updateType (value, id) {
        const payload = { id:id, type:value }
        this.props.updateType(payload)
    }
    updateName (value, id) {
        const payload = { id:id, name:value }
        this.props.updateName(payload)
        this.props.setCurrentAssetErr('')
    }
    updateDesc (value, id) {
        const payload = { id:id, desc:value }
        this.props.updateDesc(payload)
    }

    textBlur (id,type, action) {
        const payload = { id:id, [type]:false }
        this.props.setEditMode(payload, action)
    }
    showInput (id, type, action) {
        const payload = { id:id, [type]:true }
        this.props.setEditMode(payload, action)
    }

    removeAsset (id) {
        this.props.removeAsset(id)
        if(this.props.bulkUpload.assets.length == this.props.bulkUpload.maxAssets)
        {
            this.props.toggleFilesErr(false)
        }
    }

    closeModal () {
        this.props.modalVisibility(false)
    }

    openModal (id, fid) {
        this.props.modalVisibility(true)
        this.props.setCurrentAsset(id)
        this.props.setCurrentFileId(fid)
    }

    saveTags () {
        this.props.modalVisibility(false)
    }

    addTag(tag) {
        const payload = { id: this.props.bulkUpload.currentAsset, tid:tag.id, name:tag.name}
        this.props.addAssetTag(payload)
    }

    getTags () {
        if(this.props.bulkUpload.currentAsset){
            const current = this.props.bulkUpload.assets.filter(asset => asset.id === this.props.bulkUpload.currentAsset )
            return current[0].tags
        }
    }

    removeTag (id, asset) {

        const payload = {id:asset ? asset : this.props.bulkUpload.currentAsset, tid:id}
        this.props.removeAssetTag(payload)
    }

    getRowTags(id, fid) {
        this.props.setCurrentAsset(id)
        this.props.setCurrentFileId(fid)
        this.getGoogleTags(id, fid)
    }

    getGoogleTags (id, fid) {
        this.props.loadingTagsStatus(true)
        this.props.loadingAssetTags({id:id, isLoadingTags:true})
        axios.post(CCM_DISPATCHER_FILENAME + `/api/v1/tags/google-vision/process`, {id:fid})
            .then(response => {
                const tags = response.data

                tags.map(tag => {
                    this.props.addAssetTag({id:id, tid:tag.id, name:tag.name})
                })
                this.props.loadingTagsStatus(false)
                this.props.loadingAssetTags({id:id, isLoadingTags:false})
            })
    }

    render () {

        const processAssets = this.props.bulkUpload.assets.map(asset =>
            <div key={asset.id}>

                {asset.hasErrors ?
                    <div className="tb-error-msg">{asset.errorMsg}</div>
                :

                <div className="row">
                    {/* Delete Item */}
                    <div className="col-1 tb-tb tb-xs">
                        <span className="delete-asset" onClick={()=>this.removeAsset(asset.id)}><i className="fa fa-trash"></i></span>
                    </div>
                    {/* Thumbnail */}
                    <div className="col-1 tb-tb tb-sm">
                        {asset.isLoading ? <span className="loading"></span> : <img src={asset.img} />}
                    </div>
                    {/* Name */}
                    <div className="col-2 tb-tb tb-md">

                        {

                        asset.isNameEditMode ?
                            <InputText
                                value={asset.name}
                                onValueChange={(value) => this.updateName(value, asset.id)}
                                onFocusOut={()=>this.textBlur(asset.id, 'isNameEditMode', 'SET_ASSET_BULK_NAME_EDIT_MODE')}
                                onEnterPress={()=>this.textBlur(asset.id, 'isNameEditMode', 'SET_ASSET_BULK_NAME_EDIT_MODE')}
                                err = { this.props.bulkUpload.errorAsset === asset.id ? true : false }
                            /> :
                            <InputPlaceholder name={asset.name} placeholder={"Write Name here."} openInput={()=>this.showInput(asset.id, 'isNameEditMode','SET_ASSET_BULK_NAME_EDIT_MODE')}/>
                        }
                    </div>
                    {/* Description */}
                    <div className="col-2 tb-tb tb-md">
                        {asset.isDescEditMode ?
                            <InputText
                                value={asset.desc}
                                onValueChange={(value) => this.updateDesc(value, asset.id)}
                                onFocusOut={()=>this.textBlur(asset.id, 'isDescEditMode', 'SET_ASSET_BULK_DESC_EDIT_MODE')}
                                onEnterPress={()=>this.textBlur(asset.id, 'isDescEditMode', 'SET_ASSET_BULK_DESC_EDIT_MODE')}
                            /> :
                            <InputPlaceholder name={asset.desc} placeholder={"Write a Description here."} openInput={()=>this.showInput(asset.id, 'isDescEditMode','SET_ASSET_BULK_DESC_EDIT_MODE')}/>
                        }
                    </div>
                    {/* Type */}
                    <div className="col-2 tb-tb tb-sm">
                        <SelectTypeDropdown onValueChange={(value)=>this.updateType(value, asset.id)} />
                    </div>
                    {/* Tags */}
                    <div className="col-4 tb-tb">
                        <TagRowContainer
                            key={asset.id}
                            asset={asset.id}
                            file={asset.fid}
                            tags={asset.tags}
                            openModal={(id, fid)=>{this.openModal(id, fid)}}
                            getTags={(id, fid) => {this.getRowTags(id,fid)}}
                            removeTag={(tid, id)=>{this.removeTag(tid,id)}}
                            isLoading={asset.isLoadingTags}

                        />
                    </div>
                </div>
                }

            </div>
        )

        const flexTable = (
            <div className="tb-file-table">
                <div className="tb-header">
                    <div className="row">
                        <div className="col-1 tb-th tb-xs"></div>
                        <div className="col-1 tb-th tb-sm"><span>Thumbnail</span></div>
                        <div className="col-2 tb-th tb-md"><span>Name</span></div>
                        <div className="col-2 tb-th tb-md"><span>Description</span></div>
                        <div className="col-2 tb-th tb-sm"><span>Type</span></div>
                        <div className="col-4 tb-th"><span>Tags</span></div>
                    </div>
                </div>
                <div className="tb-body">
                    {processAssets}
                </div>
            </div>

        )

        return (
            <section>
                <div className="row">
                    <div className="col-12">
                        {this.props.bulkUpload.assets.length ? flexTable : null}
                    </div>
                </div>

                <Modal title="Asset Tags"
                    show={this.props.app.modal.isVisible}
                    onClose={()=>this.closeModal()}
                    onSave={()=>this.saveTags()}
                    controls="false" width="700px" height="400px">
                        <section>
                            <div className="row">
                                <div className="col-9">
                                    <label>Select Tags</label>
                                    <DropdownTags onValueChange={(tag)=>this.addTag(tag)}/>
                                </div>
                                <div className="col-3">
                                    {this.props.bulkUpload.isLoadingTags ?
                                        <span className="processing-icon"></span> :
                                        <button className="tag-gen-btn" onClick={()=>this.getGoogleTags(this.props.bulkUpload.currentAsset, this.props.bulkUpload.currentAssetFid)}>Generate Tags</button>}
                                </div>
                            </div>
                            <div className="row">
                                <div className="col-sm-12">
                                    <TagContainer tags={this.getTags()} removeTag={(id)=>this.removeTag(id)}/>
                                </div>
                            </div>
                        </section>
                </Modal>
            </section>
        )
    }
}

const mapStateToProps = (state) => {
    return {
        bulkUpload:state.bulkUpload,
        app: state.app
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        updateType: (payload) => dispatch(runAction("UPDATE_ASSET_BULK_TYPE", payload)),
        updateName: (payload) => dispatch(runAction("UPDATE_ASSET_BULK_NAME", payload)),
        updateDesc: (payload) => dispatch(runAction("UPDATE_ASSET_BULK_DESC", payload)),
        setEditMode: (payload, action) => dispatch(runAction(action, payload)),
        modalVisibility: (payload) => dispatch(runAction("SET_MODAL_VISIBILITY", payload)),
        setCurrentAsset: (payload) => dispatch(runAction("SET_CURRENT_ASSET_BULK", payload)),
        setCurrentFileId: (payload) => dispatch(runAction("SET_CURRENT_ASSET_FID_BULK", payload)),
        removeAsset: (payload) => dispatch(runAction("REMOVE_ASSET_BULK", payload)),
        addAssetTag: (payload) => dispatch(runAction("SET_ASSET_BULK_TAG", payload)),
        removeAssetTag: (payload) => dispatch(runAction("REMOVE_ASSET_BULK_TAG", payload)),
        loadingTagsStatus: (payload) => dispatch(runAction("SET_IS_LOADING_TAGS", payload)),
        setCurrentAssetErr: (payload) => dispatch(runAction("SET_ASSET_ERROR", payload)),
        loadingAssetTags: (payload) => dispatch(runAction("UPDATE_ASSET_BULK_IS_LOADING_TAGS", payload)),
        toggleFilesErr: (payload) => dispatch(runAction("SET_FILES_ERROR", payload)),
    }
}


export default connect(mapStateToProps, mapDispatchToProps)(FileTable);