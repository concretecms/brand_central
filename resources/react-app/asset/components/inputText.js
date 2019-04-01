import React, {Component} from "react"

class InputText extends Component {
    
    render (){
        const handleChange = (e) => {
            this.props.onValueChange(e.target.value)
        }

        const handleBlur = () => {
            this.props.onFocusOut()
        }

        const handleKeyPressed = (e) => {
            if (e.key === 'Enter') {
                this.props.onEnterPress()
            }    
        }

        return (
            <div>
                <input className={this.props.err ? 'error-warning' : null } 
                    value={this.props.value} onChange={handleChange} 
                    onBlur={handleBlur} onKeyPress={handleKeyPressed} 
                    placeholder={this.props.err ? 'Name is required.' : null}
                    autoFocus/>
            </div>
        )
    }
    
}

export default InputText