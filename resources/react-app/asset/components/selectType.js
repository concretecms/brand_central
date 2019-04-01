import React, { Component } from 'react'
import Select from "react-select"
import {connect} from 'react-redux'
import {setInput} from '../actions/assetActions'

const customStyle = {
    control: styles => ({
        ...styles,
        backgroundColor: '#ffffff',
        borderRadius: 0,
        width:'250px',
        height:'45px',
        boxShadow:'none',
        borderColor:'#ccc'
    })
}

let current = null

class SelectType extends Component {
    
    render (){

        current = this.props.app.selectTypeOptions.findIndex(i => i.value === this.props.asset.type)

        const handleChange = (type) => {
            this.props.setError(false)
            this.props.inputChanged(type.value)
        }
        
        return (
            <div>
                <label>{this.props.label} <span>{this.props.required ? "*" : "" }</span></label>
                <div className={this.props.error ? "error-warning":''} style={{'width':'252px'}}>
                    <Select 
                        value={this.props.app.selectTypeOptions[current]} 
                        options={this.props.app.selectTypeOptions} 
                        styles={customStyle} 
                        onChange={handleChange}
                        classNamePrefix="select"
                        selectedValue={this.props.asset.type}
                        filterOption={false}
                        isSearchable={false}
                    />
                </div>
                {this.props.app.errorType ? <span className="alert alert-danger alert-select-type">Select a Type</span> : null}
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
        inputChanged : (type) => dispatch(setInput("SET_ASSET_TYPE",type)),
        setError : (payload) => dispatch(setInput("SET_ERROR_TYPE", payload))
    }
}
export default connect(mapStateToProps, mapDispatchToProps )(SelectType)