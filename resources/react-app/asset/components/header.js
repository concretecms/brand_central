import React, { Component } from "react";

class Header extends Component {
  render() {
    return (
      <div className="row">
        <div className="col-md-7">
            {this.props.showTittle ? <h3>{this.props.asset.id ? "Edit" : "Create"} Asset</h3> : null}
        </div>
        <div className="col-md-5 text-right">
          <button
            className="btn-clear"
            disabled={this.props.disabled}
            onClick={this.props.clickCancel}
          >
            Cancel
          </button>
          <button
            className="btn-bold"
            disabled={this.props.disabled}
            onClick={this.props.clickSave}
          >
            {this.props.asset.id ? "Save" : "Create"}
          </button>
        </div>
      </div>
    );
  }
}

export default Header;
