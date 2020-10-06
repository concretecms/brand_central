import React, {Component} from 'react'
import {connect} from 'react-redux'

import {fetchAsset, runAction} from '../actions/assetActions'

import Header from '../components/header'
import Input from '../components/input'
import TextArea from '../components/textArea'
import SelectType from '../components/selectType'
import SelectDropdown from '../components/selectDropdown'
import SelectCollections from '../components/selectCollections'
import TagsGeneratorBtn from '../components/tagsGenerator'
import Thumbnail from './thumbnail'
import Files from './files'

import AssetCollections from './collections'
import {createAsset, updateAsset} from '../lib/assetServices'

class Asset extends Component {

    constructor(props) {
        // Call parent constructor
        super(props);

        // Set our local state
        this.state = {
            saving: false
        }
    }

    componentDidMount() {
        if (typeof this.props.assetId !== 'undefined') {
            this.props.fetchAsset(this.props.assetId)
        }
    }

    handleCancel() {
        console.log(this.props.asset.id);
        if (this.props.asset.id) {
            window.location.href = CCM_DISPATCHER_FILENAME + '/assets/' + this.props.asset.id
        }
        else {
            window.location.href = CCM_DISPATCHER_FILENAME + '/account/welcome'
        }
    }

    handleClick() {
        if (this.props.asset.name === "") {
            return this.handleError("MISSING_NAME")
        }
        if (this.props.asset.type === "") {
            return this.handleError("MISSING_TYPE")
        }
        if (this.props.asset.files.length === 0) {
            return this.handleError("MISSING_FILES")
        }
        if (this.props.asset.collections.length === 0) {
            return this.handleError("MISSING_COLLECTION")
        }

        // Change state to track that we are actively saving, this disbles our buttons
        this.toggleSaving(true)


        let promise;
        if (this.props.asset && this.props.asset.id) {
            promise = updateAsset(this.props.asset);
        } else {
            promise = createAsset(this.props.asset);
        }

        // Bind callbacks to our promise then / otherwise
        promise.then(
            (response) => {
                if (response.id) {
                    window.location.href = CCM_DISPATCHER_FILENAME + '/assets/' + response.id;
                } else {
                    this.toggleSaving(false)
                }
            },
            () => {
                this.toggleSaving(false)
            }
        );
    }

    handleError(error) {
        switch (error) {
            case "MISSING_NAME" :
                this.props.toggleNameErr(true)
                break
            case "MISSING_TYPE":
                this.props.toggleTypeErr(true)
                break
            case "MISSING_COLLECTION":
                this.props.toggleCollectionsErr(true)
                break
            case "MISSING_FILES":
                this.props.toggleFilesErr(true)
                break
        }
    }

    handleInputBlur(event) {
        if (event.target.value === '') {
            this.handleError("MISSING_NAME")
        } else {
            this.props.toggleNameErr(false)
        }
    }

    errorMsg(err) {
        return (
            <span className="alert alert-danger">
                <i className="fa fa-exclamation-triangle"></i> {err}
            </span>
        )
    }

    /**
     * Change our local state to track whether we're actively saving
     *
     * This method does nothing if the saving state isn't actually changing. If it is changing, it will set the local
     * state then run a bound `toggleSaving` property that was defined in our mapDispatchToProps function
     *
     * @param saving
     */
    toggleSaving(saving) {
        let savingBool = !!saving;

        if (savingBool !== this.state.saving) {
            this.state.saving = savingBool;
            this.props.toggleSaving(savingBool);
        }
    }

    render() {

        return (
            <div className="asset-app">
                {this.state.saving ?
                    <div className="processing-bulk-upload"><span className="loading"></span></div> : null}

                <Header showTittle={true} clickSave={() => {
                    this.handleClick()
                }} clickCancel={() => {
                    this.handleCancel()
                }} asset={this.props.asset} disabled={this.state.saving}/>
                <section>
                    <div className="row">
                        <div className="col-md-6 col-12">
                            <p>Asset Information</p>
                            <section>
                                <Input label={'Name'} required={true} action={'SET_ASSET_NAME'} field={'name'}
                                       error={this.props.app.errorName}
                                       focusOut={(event) => this.handleInputBlur(event)}/>
                            </section>
                        </div>
                        <div className="col-md-6 col-12" style={{'height': '95px'}}>
                            {this.props.app.errorName ? this.errorMsg("Missing name") : null}
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-6 col-12">
                            <section>
                                <SelectType label={'type'} required={true} error={this.props.app.errorType}/>

                            </section>
                        </div>
                        <div className="col-md-6 col-12">
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-6 col-12">
                            <Thumbnail/>
                        </div>
                        <div className="col-md-6 col-12">
                            <Files files={this.props.asset.files} error={this.props.app.errorFiles}/>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-12">
                            <section>
                                <TextArea label={'Description'} required={false} action={'SET_ASSET_DESC'}/>
                            </section>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-6 col-12">
                            <section>
                                <SelectCollections label={'Add to Collection'} required={true}
                                                   action={'FETCH_COLLECTIONS'} actionReducer={'SET_ASSET_COLLECTIONS'}
                                                   error={this.props.app.errorCollections}/>
                            </section>
                        </div>
                        <div className="col-md-6 col-12" style={{height: '75px'}}>
                            {this.props.app.errorCollections ? this.errorMsg("Please select a Collection") : null}
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-12">
                            <section>
                                <AssetCollections field={'collections'} action={'REMOVE_COLLECTION'}/>
                            </section>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-6 col-12">
                            <section>
                                <SelectDropdown label={'Tags'} required={false} action={'FETCH_TAGS'}
                                                actionReducer={'SET_ASSET_TAGS'} field={'tags'} dropdown={false}/>
                            </section>
                        </div>
                        <div className="col-md-6 col-12">
                            <section>
                                <TagsGeneratorBtn/>
                            </section>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-12">
                            <section>
                                <AssetCollections field={'tags'} action={'REMOVE_TAG'}/>
                            </section>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-6 col-12">
                            <section>
                                <Input label={'Location'} required={false} action={'SET_ASSET_LOCATION'}
                                       field={'location'}/>
                            </section>
                        </div>
                    </div>
                </section>
                <Header showTittle={false} clickSave={() => {
                    this.handleClick()
                }} clickCancel={() => {
                    this.handleCancel()
                }} asset={this.props.asset} disabled={this.state.saving}/>
            </div>
        )
    }
}

const mapStateToProps = (state) => {
    return {
        asset: state.asset,
        app: state.app
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        fetchAsset: (payload) => dispatch(fetchAsset(payload)),
        toggleNameErr: (payload) => dispatch(runAction("SET_ERROR_NAME", payload)),
        toggleTypeErr: (payload) => dispatch(runAction("SET_ERROR_TYPE", payload)),
        toggleCollectionsErr: (payload) => dispatch(runAction("SET_ERROR_COLLECTIONS", payload)),
        toggleFilesErr: (payload) => dispatch(runAction("SET_ERROR_FILES", payload)),

        // Handle rerendering when saving state changes
        toggleSaving: (payload) => dispatch(runAction("TOGGLE_SAVING", payload))
    }
}
export default connect(mapStateToProps, mapDispatchToProps)(Asset);
