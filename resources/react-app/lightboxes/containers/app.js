import React, { Component } from 'react'
import Modal from "../components/modal"
import List from "../components/list"
import Input from "../components/inputText"
import Banner from "../components/banner"
import axios from "axios";

class App extends Component {

    constructor() {
        super()
        this.state = {
            lightboxes:[],
            entryName:'',
            isLoading:false,
            showMsgBanner:false,
            msgBanner:'',
            msgBannerErr:true
        }
    }

    closeModal() {
        this.props.closeModal()
    }

    closeBanner() {
        setTimeout(() => {
            this.setState(()=> ({
                showMsgBanner:false,
                msgBanner:'',
                msgBannerErr:true
            }))
        }, 3500)
    }

    openBanner(msg, err) {
        this.setState(()=> ({
            showMsgBanner:true,
            msgBanner:msg,
            msgBannerErr:err
        }))
    }

    handleItemClick(id) {
        axios.put(CCM_DISPATCHER_FILENAME + '/api/v1/lightboxes/set', {id:id, asset:this.props.asset}, {headers :{'X-Requested-With': 'XMLHttpRequest'}})
        .then(
            response => {
                this.closeModal()
                this.openBanner('Asset Added to Lightbox', false)
                this.closeBanner()
            }
        ).catch(error => {
            this.openBanner('Unable to add asset to lightbox', true)
            this.closeBanner()
        })
    }

    handleInputText(value) {
        this.setState(()=>({entryName:value}))
    }

    saveLightBox() {
        if(this.state.entryName == undefined || this.state.entryName == ''){
            this.openBanner('Please specify a lightbox name', true)
            this.closeBanner()
        } else {
            axios.post(CCM_DISPATCHER_FILENAME + '/api/v1/lightboxes/create', {lightbox:this.state.entryName, asset:this.props.asset}, {headers :{'X-Requested-With': 'XMLHttpRequest'}})
              .then(
                response => {
                    this.closeModal()
                    this.openBanner('Lightbox created and Asset added!', false)
                    this.closeBanner()
                }
              ).catch(error => {
                this.openBanner('Unable to add asset to new lightbox', true)
                this.closeBanner()
            })
        }
    }

    getLightboxes () {
        this.setState(()=>({ isLoading: true }))
        axios.get(CCM_DISPATCHER_FILENAME + '/api/v1/lightboxes', {}, {headers :{'X-Requested-With': 'XMLHttpRequest'}})
        .then(response => {
            this.setState(()=>({
                lightboxes: response.data,
                isLoading: false
            }))
        }).catch(error => {
            this.openBanner('Unable to load lightboxes', true)
            this.closeBanner()
        })
    }

    componentDidMount() {
        this.getLightboxes()
    }

    componentWillReceiveProps(){
        if(!this.props.showModal){
            this.getLightboxes()
        }
    }

    render() {

        return (
            <div>
                <Modal title="Add to Lightbox"
                    show={this.props.showModal}
                    onClose={()=>this.closeModal()}
                    controls={false} width="450px" height="500px">
                        <div>
                            <List items={this.state.lightboxes} isLoading={this.state.isLoading} itemClicked={(id)=>this.handleItemClick(id)}/>
                            <Input onValueChange={(value) => this.handleInputText(value)} saveEntry={()=>this.saveLightBox()}/>
                        </div>
                </Modal>
                <Banner show={this.state.showMsgBanner} msg={this.state.msgBanner} err={this.state.msgBannerErr}/>
            </div>
        )
    }
}

export default App
