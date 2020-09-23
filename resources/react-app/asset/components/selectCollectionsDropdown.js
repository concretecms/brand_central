import React, { Component } from 'react'
import AsyncCreatableSelect, {createFilter} from 'react-select/lib/AsyncCreatable'
import _ from 'underscore'
import axios from 'axios'

const customStyle = {
    control: styles => ({
        ...styles,
        backgroundColor: '#ffffff',
        borderRadius: 0,
        height:'45px',
        boxShadow:'none',
        borderColor:'#ccc'
    })
}

class DropdownCollections extends Component  {
    constructor(props) {
        super(props);
        this.state = {
            value: ''
        };
    }
    render () {

        const handleChange = (payload) => {
            let item = {id: payload.value, name: payload.label}
            this.props.onValueChange(item)
        }

        const handleCreate = (inputValue) => {

            axios.post(CCM_DISPATCHER_FILENAME + "/api/v1/collections/create", {collection: inputValue}, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
              .then(response => {
                  const optionId = response.data.collection
                  const newOption = {id: optionId, name: inputValue}
                  this.props.onValueChange(newOption)
              })
        }

        const getOptions = (inputValue) => {

            if (!this.debouncer) {
                let me = this;
                this.promise = new Promise(function(resolve, reject) {
                    me.resolve = resolve;
                });

                this.debouncer = _.debounce(function(inputValue) {
                    this.promise = null;
                    this.debouncer = null;

                    axios.get(CCM_DISPATCHER_FILENAME + `/api/v1/collections?search=${inputValue}`)
                        .then(response => {
                            me.resolve(response.data.map(option => ({value: option.id, label: option.name})));
                        })
                }, 600);
            }

            this.debouncer(inputValue);
            return this.promise;
        }

        return (
            <div>
                <label>{this.props.label} <span>{this.props.required ? "*" : "" }</span></label>
                <div className={ this.props.error ? "error-warning" : "select-wrapper" }>
                    <AsyncCreatableSelect
                        value={this.state.value}
                        styles={customStyle}
                        classNamePrefix="select"
                        loadOptions={getOptions}
                        defaultOptions
                        onChange={handleChange}
                        onCreateOption={handleCreate}
                        isValidNewOption={(inputValue, selectValue, selectOptions) => {
                            const isNotDuplicated = !selectOptions
                                .map(option => option.label)
                                .includes(inputValue);
                                const isNotEmpty = inputValue !== '';
                                return isNotEmpty && isNotDuplicated;
                                }}
                    />
                </div>
            </div>
        )
    }
}

export default DropdownCollections
