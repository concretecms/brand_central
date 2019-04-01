import React, {Component} from "react"

class InputPlaceholder extends Component {

    render (){

        const handleClick = () => {
            this.props.openInput()
        }

        return (
            <div onFocus={handleClick} tabIndex="0">
                {this.props.name ?
                    <span className="input-container" onClick={handleClick}>{this.props.name}</span> :
                    <span className="placeholder" onClick={handleClick}>{this.props.placeholder}</span>}
            </div>
        )
    }

}

export default InputPlaceholder
