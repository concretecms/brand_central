import React from 'react'
import {connect} from 'react-redux'
import {setInput} from '../actions/assetActions'

const TextArea = (props) => {
    return (
        <div>
            <label>{props.label} <span>{props.required ? "*" : "" }</span></label>
            <textarea value={props.asset.desc} onChange={props.inputChanged}/>    
        </div>
    )
}

const mapStateToProps = (state) => {
    return {
        asset:state.asset,
    }
}

const mapDispatchToProps = (dispatch, props) => {
    return {
        inputChanged : (event) => dispatch(setInput(props.action,event.target.value))
    }
}
export default connect(mapStateToProps, mapDispatchToProps)(TextArea)