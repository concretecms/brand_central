import React, { Component } from 'react'
import {connect} from 'react-redux'
import { runAction } from '../actions/assetActions';
import axios from 'axios';

class TagsGeneratorBtn extends Component {

    render () {
        
        const getGoogleLabels = () => {
            this.props.updateTagsLoader(true)
            const fileIds = this.props.asset.files.map(item => item.fid)
            axios.post(CCM_DISPATCHER_FILENAME + `/api/v1/tags/google-vision/process/images`, {files:fileIds}, { headers : {'X-Requested-With': 'XMLHttpRequest'} })
                .then(response => {
                    const tags = response.data
                    tags.map(tag => {
                        this.props.updateTags({id:tag.id, name:tag.name})
                    })
                    this.props.updateTagsLoader(false)
                    this.props.updateBtnVisibility(false)
                }).catch((error) => {
                    this.props.updateTagsLoader(false)
                })
        }


        const genTagsBtn = (
            <span className="tag-gen-btn" onClick={()=>getGoogleLabels()}>Generate Tags</span>
        )
        
        return (
            <div>
                { this.props.asset.files.length && this.props.app.tagsGenerator.isBtnVisible ? genTagsBtn : null}
                { this.props.app.tagsGenerator.isLoading ? <span className="processing-icon"></span> : null }
            </div>
            
        )
    }

}

const mapStateToProps = (state) => {
    return {
        asset:state.asset,
        app:state.app
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        updateTags : (payload) => dispatch(runAction("SET_ASSET_TAGS",payload)),
        updateTagsLoader : (payload) => dispatch(runAction("SET_GENTAGS_IS_LOADING", payload)),
        updateBtnVisibility: (payload) => dispatch(runAction("SET_GENTAGS_BTN_IS_VISIBLE", payload))
    }
}
export default connect(mapStateToProps, mapDispatchToProps )(TagsGeneratorBtn)