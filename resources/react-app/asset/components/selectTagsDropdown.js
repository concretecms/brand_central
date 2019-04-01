import React, {Component} from "react"
import AsyncCreatableSelect from 'react-select/lib/AsyncCreatable'
import {transactionId} from "../lib/assetServices"
import _ from 'underscore'
import axios from 'axios'

const customStyle = {
    control: styles => ({
        ...styles,
        backgroundColor: '#ffffff',
        borderRadius: 0,
        height: '45px',
        boxShadow: 'none',
        borderColor: '#ccc'
    })
}

class DropdownTags extends Component {
    constructor(props) {
        super(props);
        this.state = {value: ''};
    }

    render() {

        const getOptions = (inputValue) => {
            if (!this.debouncer) {
                let me = this;
                this.promise = new Promise(function (resolve, reject) {
                    me.resolve = resolve;
                });

                this.debouncer = _.debounce(function (inputValue) {
                    this.promise = null;
                    this.debouncer = null;

                    axios.get(`/api/v1/tags?search=${inputValue}`)
                        .then(response => {
                            me.resolve(response.data.map(option => ({value: option.id, label: option.name})));
                        })
                }, 600);
            }
            this.debouncer(inputValue);
            return this.promise;
        }

        const addItem = (payload) => {
            const utid = transactionId();
            let tag = {id: utid, name: payload.label}
            this.props.onValueChange(tag)
        }

        return (
            <section>
                <AsyncCreatableSelect
                    value={this.state.value}
                    styles={customStyle}
                    classNamePrefix="select"
                    loadOptions={getOptions}
                    defaultOptions={false}
                    onChange={addItem}
                    isValidNewOption={(inputValue, selectValue, selectOptions) => {
                        const isNotDuplicated = !selectOptions
                            .map(option => option.label)
                            .includes(inputValue);
                        const isNotEmpty = inputValue !== '';
                        return isNotEmpty && isNotDuplicated;
                    }}
                />
            </section>
        )
    }
}

export default DropdownTags