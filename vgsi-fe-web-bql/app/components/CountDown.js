import React, { Component } from "react";

class CountDown extends Component {
  constructor(props) {
    super(props);
    this.state = {
      seconds: props.time,
    };
  }

  componentDidMount() {
    this.count();
  }

  count = () => {
    this.intervalId = setInterval(() => {
      if (this.state.seconds > 0) {
        this.setState({ seconds: this.state.seconds - 1 });
      } else {
        clearInterval(this.intervalId);
      }
    }, 1000);
  };

  UNSAFE_componentWillReceiveProps(nextProps) {
    if (this.props.time != nextProps.time) {
      this.setState({
        seconds: nextProps.time,
      });
    }
    if (this.props.reset != nextProps.reset) {
      this.setState({
        seconds: nextProps.time,
      });
      this.count();
    }
  }

  componentWillUnmount() {
    clearInterval(this.intervalId);
  }

  render() {
    const { prefix, children, suffix } = this.props;
    return (
      <>
        {this.state.seconds === 0 ? (
          <span>{children}</span>
        ) : (
          <span>
            {prefix} {this.state.seconds} s {suffix}
          </span>
        )}
      </>
    );
  }
}

export default CountDown;
